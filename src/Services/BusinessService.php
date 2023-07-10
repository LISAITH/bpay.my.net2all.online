<?php

namespace App\Services;

use App\Entity\BPayMomoTransaction;
use App\Entity\BPayProviderItem;
use App\Entity\BPayTransaction;
use App\Entity\CompteBPay;
use App\Entity\DTO\Transaction;
use App\Entity\Recharges;
use App\Entity\ReglementBPayLink;
use App\Entity\SousCompte;
use App\Entity\User;
use App\Entity\VirementsBPay;
use App\Services\APIService\ApiUser;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;

class BusinessService
{
    private $entityManager;
    private $bPayConfigService;
    private $apiUser;

    public function __construct(EntityManagerInterface $entityManager, BPayConfigService $configService,
        ApiUser $apiUser)
    {
        $this->bPayConfigService = $configService;
        $this->entityManager = $entityManager;
        $this->apiUser = $apiUser;
    }

    public function createECashAccount(CompteBPay $eCashAccount): void
    {
        if (empty($eCashAccount->getNumeroCompte())) {
            throw new \Exception('AccountNumber could\'nt be empty');
        }

        if (!$this->checkECashAccountByNumber($eCashAccount->getNumeroCompte())) {
            $this->entityManager->persist($eCashAccount);
            $this->entityManager->flush();
        }
    }

    public function getECashAccountByUser(User $user): ?CompteBPay
    {
        $eCashAccount = null;

        switch (\strtoupper($user->getType())) {
            case constants::USER_TYPE_SYS_NAME_ENTREPRISE:
                $findEntreprise = $this->apiUser->getEntreprise($user->getEntrepriseId());
                if (empty($findEntreprise)) {
                    throw new \Exception('User unknown');
                }
                $eCashAccount = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                    'entreprise_id' => (int) $findEntreprise->getId(),
                ]);
                break;
            case constants::USER_TYPE_SYS_NAME_PARTICULIER:
                $findParticular = $this->apiUser->getParticular($user->getParticulierId());
                if (empty($findParticular)) {
                    throw new \Exception('User unknown');
                }
                $eCashAccount = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                    'particulier_id' => $findParticular->getId(),
                ]);
                break;
            default:
                break;
        }

        return $eCashAccount;
    }

    public function getECashAccountByNumber(string $accountNumber): ?CompteBPay
    {
        if (empty($accountNumber)) {
            throw new \Exception('Account number could\'nt be empty');
        }

        return $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
            'numeroCompte' => $accountNumber,
        ]);
    }

    public function getUserRecharges(User $user): ?array
    {
        $findBPayAccount = $this->apiUser->getUserAccountBPay($user);
        if (empty($findBPayAccount)) {
            throw new \Exception('Not found');
        }

        return $this->entityManager->getRepository(Recharges::class)->findBy([
            'compteBPay' => $findBPayAccount,
        ]);
    }

    public function getExternalTransferts(User $user): ?array
    {
        $findBPayAccount = $this->apiUser->getUserAccountBPay($user);
        if (empty($findBPayAccount)) {
            throw new \Exception('Not found');
        }

        return $this->entityManager->getRepository(VirementsBPay::class)->findBy([
            'idCompteEnvoyeur' => $findBPayAccount,
        ]);
    }

    public function getUserReglements(User $user): ?array
    {
        return $this->entityManager->getRepository(ReglementBPayLink::class)->findBy([
            'userSender' => $user->getId(),
        ]);
    }

    public function getECashAccountById(int $eCashAccountId): ?CompteBPay
    {
        return $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
            'id' => $eCashAccountId,
        ]);
    }

    public function creditECashAccount(CompteBPay $eCash, int $amount): bool
    {
        if (0 == $amount) {
            throw new \Exception('Amount must be greater  0');
        }

        if (!$this->checkECashAccountByNumber($eCash->getNumeroCompte())) {
            throw new \Exception('Ecash Account does\'nt exists');
        }

        $eCash->setSolde($eCash->getSolde() + $amount);
        $this->entityManager->persist($eCash);
        $this->entityManager->flush();

        return true;
    }

    public function creditUserSubAccount(SousCompte $eCash, int $amount): bool
    {
        if (0 == $amount) {
            throw new \Exception('Amount must be greater  0');
        }

        $eCash->setSolde($eCash->getSolde() + $amount);
        $this->entityManager->persist($eCash);
        $this->entityManager->flush();

        return true;
    }

    public function debitECashAccount(CompteBPay $eCash, int $amount): bool
    {
        if (0 == $amount) {
            throw new \Exception('Amount must be greater  0');
        }

        if (!$this->checkECashAccountByNumber($eCash->getNumeroCompte())) {
            throw new \Exception('Ecash Account does\'nt exists');
        }

        if ($eCash->getSolde() < $amount || ($eCash->getSolde() - $amount) < 0) {
            throw new \Exception('Could\'nt debit ecash account. Insuffisant Amount');
        }

        $eCash->setSolde($eCash->getSolde() - $amount);
        $this->entityManager->persist($eCash);
        $this->entityManager->flush();

        return true;
    }

    public function debitEcashSubAccount(SousCompte $sousCompte, int $amount): bool
    {
        if (0 == $amount) {
            throw new \Exception('Amount must be greater  0');
        }

        if ($sousCompte->getSolde() < $amount || ($sousCompte->getSolde() - $amount) < 0) {
            throw new \Exception('Could\'nt debit ecash sous-account. Insuffisant Amount');
        }

        $sousCompte->setSolde($sousCompte->getSolde() - $amount);
        $this->entityManager->persist($sousCompte);
        $this->entityManager->flush();

        return true;
    }

    public function checkECashAccountByNumber(string $accountNumber): ?bool
    {
        $accountIsFind = false;
        if (!empty($this->getECashAccountByNumber($accountNumber))) {
            $accountIsFind = true;
        }

        return $accountIsFind;
    }

    public function getUserTransactionListCount(User $user): int
    {
        return \count($this->entityManager->getRepository(BPayTransaction::class)->findBy([
            'user_id' => $user->getId(),
        ]));
    }

    public function getUserTransactionListV2(?User $user): array
    {
        return $this->entityManager->getRepository(BPayTransaction::class)->findBy([
            'user_id' => $user->getId(),
        ], [
            'transaction_date' => 'DESC',
        ]);
    }

    public function getUserTransactionList(?User $user): array
    {
        $transactionListFormat = [];
        try {
            $transactionList = $this->entityManager->getRepository(BPayTransaction::class)->findBy([
                'user_id' => $user->getId(),
            ], [
                'transaction_date' => 'DESC',
            ]);
            foreach ($transactionList as $bPayTrans) {
                $transItem = new Transaction();
                $transItem->setId($bPayTrans->getId());
                $transItem->setGuid($bPayTrans->getGuid());
                switch ($bPayTrans->getTransactionTypeName()) {
                    case constants::RECHARGEMENT_BPAY:
                        switch ($bPayTrans->getProviderCode()) {
                            case constants::MOMO_PROVIDER_CODE:
                                $getProvider = $this->entityManager->getRepository(BPayProviderItem::class)->findOneBy([
                                    'providerCode' => constants::MOMO_PROVIDER_CODE,
                                ]);
                                $findMomoTrans = $this->entityManager->getRepository(BPayMomoTransaction::class)
                                    ->findOneBy(['xReferenceId' => $bPayTrans->getGuid()]);
                                if (!empty($findMomoTrans) && $findMomoTrans instanceof BPayMomoTransaction) {
                                    $transItem->setProviderName(constants::MOMO_PROVIDER_CODE);
                                    $transItem->setAmountPayed($bPayTrans->getAmount());
                                    $transItem->setCurrency($bPayTrans->getCurrency());
                                    $transItem->setStatus($bPayTrans->getStatus());
                                    $transItem->setProviderImg($getProvider->getBrand());
                                    $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                                    $transItem->setProviderName($getProvider->getName());
                                    $transItem->setHolderName($findMomoTrans->getHolderfullName());
                                    if ('SUCCESSFUL' == $bPayTrans->getStatus()) {
                                        $transItem->setTransactionID($findMomoTrans->getTransactionRef());
                                    }
                                    $transItem->setReason($transItem->getReason());
                                    $transItem->setOperationType($bPayTrans->getTypeOpteration());
                                    $transItem->setNote($bPayTrans->getNote());
                                    $transItem->setTransactionTypeName($this->getTransactionTypeValue($bPayTrans->getTransactionTypeName()));
                                    $transactionListFormat[] = $transItem;
                                }
                                break;
                            case constants::STRIPE_PROVIDER_CODE:
                                $getProvider = $this->entityManager->getRepository(BPayProviderItem::class)->findOneBy([
                                    'providerCode' => constants::STRIPE_PROVIDER_CODE,
                                ]);
                                $transItem->setProviderName(constants::STRIPE_PROVIDER_CODE);
                                $transItem->setAmountPayed($bPayTrans->getAmount());
                                $transItem->setCurrency($bPayTrans->getCurrency());
                                $transItem->setStatus($bPayTrans->getStatus());
                                $transItem->setProviderImg($getProvider->getBrand());
                                $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                                // $transItem->setHolderName($bPayTrans->getCreditCard()->getHolderName());
                                /*  if ('SUCCESSFUL' == $bPayTrans->getStatus()) {
                                      $transItem->setTransactionID($bPayTrans->getCreditCard()->getReference());
                                  }*/
                                $transItem->setReason($transItem->getReason());
                                $transItem->setOperationType($bPayTrans->getTypeOpteration());
                                $transItem->setTransactionTypeName($this->getTransactionTypeValue($bPayTrans->getTransactionTypeName()));
                                $transItem->setNote($bPayTrans->getNote());
                                $transactionListFormat[] = $transItem;
                                break;
                            case constants::MOOV_PROVIDER_CODE:
                            case constants::CELTTIS_PROVIDER_CODE:
                                break;
                        }
                        break;
                    case constants::TRANSFERT_BPAY:
                        $transItem->setProviderName($this->getTransactionMethodTypeValue(constants::TRANSFERT_BPAY));
                        $transItem->setAmountPayed($bPayTrans->getAmount());
                        $transItem->setCurrency($bPayTrans->getCurrency());
                        $transItem->setStatus($bPayTrans->getStatus());
                        $transItem->setProviderImg(constants::TRANSFER_BPAY_IMG);
                        $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                        $transItem->setReason($transItem->getReason());
                        $transItem->setNote($bPayTrans->getNote());
                        $transItem->setOperationType($bPayTrans->getTypeOpteration());
                        $transItem->setTransactionTypeName($this->getTransactionTypeValue($bPayTrans->getTransactionTypeName()));
                        // $transItem->setNote(sprintf('TRANSFERT BPAY:%s - %s', $bPayTrans->getVirementsEcash()->getIdCompteEnvoyeur()->getNumeroCompte(), $bPayTrans->getVirementsEcash()->getIdCompteReceveur()->getNumeroCompte()));
                        $transactionListFormat[] = $transItem;
                        break;
                    case constants::REGLEMENT_BPAY:
                        $transItem->setProviderName($this->getTransactionMethodTypeValue(constants::REGLEMENT_BPAY));
                        $transItem->setAmountPayed($bPayTrans->getAmount());
                        $transItem->setCurrency($bPayTrans->getCurrency());
                        $transItem->setStatus($bPayTrans->getStatus());
                        $transItem->setProviderImg(constants::TRANSFER_BPAY_IMG);
                        $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                        $transItem->setReason($transItem->getReason());
                        $transItem->setNote($bPayTrans->getNote());
                        $transItem->setOperationType($bPayTrans->getTypeOpteration());
                        $transItem->setTransactionTypeName($this->getTransactionTypeValue($bPayTrans->getTransactionTypeName()));
                        // $transItem->setNote(sprintf('TRANSFERT BPAY:%s - %s', $bPayTrans->getVirementsEcash()->getIdCompteEnvoyeur()->getNumeroCompte(), $bPayTrans->getVirementsEcash()->getIdCompteReceveur()->getNumeroCompte()));
                        $transactionListFormat[] = $transItem;
                        break;
                    case constants::VIREMENT_BPAY_BANK:
                    case constants::VIREMENT_INTER_BANK:
                    default:
                        break;
                }
            }
        } catch (\Exception $e) {
            throw $e;
        }

        return $transactionListFormat;
    }

    public function getTransactionTypeValue(string $type): ?string
    {
        switch ($type) {
            case constants::REGLEMENT_BPAY:
                return 'REGLEMENT B-PAY';
            case constants::RECHARGEMENT_BPAY:
                return 'RECHARGEMENT B-PAY';
            case constants::TRANSFERT_BPAY:
                return 'TRANSFERT B-PAY';
        }
    }

    public function getTransactionMethodTypeValue(string $type): ?string
    {
        switch ($type) {
            case constants::REGLEMENT_BPAY:
            case constants::RECHARGEMENT_BPAY:
            case constants::TRANSFERT_BPAY:
                return 'B-PAY';
            default:
                return $type;
        }
    }

    public function convertBPayTransactionToTransactionData(BPayTransaction $bPayTrans): ?Transaction
    {
        $transItem = new Transaction();
        $transItem->setId($bPayTrans->getId());
        $transItem->setGuid($bPayTrans->getGuid());
        switch ($bPayTrans->getTransactionTypeName()) {
            case constants::RECHARGEMENT_BPAY:
                switch ($bPayTrans->getProviderCode()) {
                    case constants::MOMO_PROVIDER_CODE:
                        $getProvider = $this->entityManager->getRepository(BPayProviderItem::class)->findOneBy([
                            'providerCode' => constants::MOMO_PROVIDER_CODE,
                        ]);
                        $findMomoTrans = $this->entityManager->getRepository(BPayMomoTransaction::class)
                            ->findOneBy(['xReferenceId' => $bPayTrans->getGuid()]);
                        if (!empty($findMomoTrans) && $findMomoTrans instanceof BPayMomoTransaction) {
                            $transItem->setProviderName(constants::MOMO_PROVIDER_CODE);
                            $transItem->setAmountPayed($bPayTrans->getAmount());
                            $transItem->setCurrency($bPayTrans->getCurrency());
                            $transItem->setStatus($bPayTrans->getStatus());
                            $transItem->setProviderImg($getProvider->getBrand());
                            $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                            $transItem->setProviderName($getProvider->getName());
                            $transItem->setHolderName($findMomoTrans->getHolderfullName());
                            if ('SUCCESSFUL' == $bPayTrans->getStatus()) {
                                $transItem->setTransactionID($findMomoTrans->getTransactionRef());
                            }
                            $transItem->setReason($transItem->getReason());
                            $transItem->setOperationType($bPayTrans->getTypeOpteration());
                            $transItem->setTransactionTypeName('RECHARGEMENT B-PAY');
                            $transactionListFormat[] = $transItem;
                        }
                        break;
                    case constants::STRIPE_PROVIDER_CODE:
                        $getProvider = $this->entityManager->getRepository(BPayProviderItem::class)->findOneBy([
                            'providerCode' => constants::STRIPE_PROVIDER_CODE,
                        ]);
                        $transItem->setProviderName(constants::STRIPE_PROVIDER_CODE);
                        $transItem->setAmountPayed($bPayTrans->getAmount());
                        $transItem->setCurrency($bPayTrans->getCurrency());
                        $transItem->setStatus($bPayTrans->getStatus());
                        $transItem->setProviderImg($getProvider->getBrand());
                        $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                        //$transItem->setHolderName($bPayTrans->getCreditCard()->getHolderName());
                      /*  if ('SUCCESSFUL' == $bPayTrans->getStatus()) {
                            $transItem->setTransactionID($bPayTrans->getCreditCard()->getReference());
                        }*/
                        $transItem->setReason($transItem->getReason());
                        $transItem->setOperationType($bPayTrans->getTypeOpteration());
                        $transItem->setTransactionTypeName($bPayTrans->getTransactionTypeName());
                     /*   $transItem->setNote(sprintf('Carte:%s - %s', $bPayTrans->getCreditCard()->getCardNumber(), strtoupper($bPayTrans->getCreditCard()->getBrand())));
                    */    $transactionListFormat[] = $transItem;
                        break;
                    case constants::MOOV_PROVIDER_CODE:
                    case constants::CELTTIS_PROVIDER_CODE:
                        break;
                }
                break;
            case constants::TRANSFERT_BPAY:
                $transItem->setProviderName(constants::TRANSFERT_BPAY);
                $transItem->setAmountPayed($bPayTrans->getAmount());
                $transItem->setCurrency($bPayTrans->getCurrency());
                $transItem->setStatus($bPayTrans->getStatus());
                $transItem->setProviderImg(constants::TRANSFER_BPAY_IMG);
                $transItem->setPaymentDate($bPayTrans->getTransactionDate());
                $transItem->setReason($transItem->getReason());
                $transItem->setOperationType($bPayTrans->getTypeOpteration());
                $transItem->setTransactionTypeName($bPayTrans->getTransactionTypeName());
                // $transItem->setNote(sprintf('TRANSFERT BPAY:%s - %s', $bPayTrans->getVirementsEcash()->getIdCompteEnvoyeur()->getNumeroCompte(), $bPayTrans->getVirementsEcash()->getIdCompteReceveur()->getNumeroCompte()));
                $transactionListFormat[] = $transItem;
                break;
            case constants::REGLEMENT_BPAY:
            case constants::VIREMENT_BPAY_BANK:
            case constants::VIREMENT_INTER_BANK:
            default:
                break;
        }

        return $transItem;
    }

    public function getSubAccountsByUser(User $user): ?array
    {
        $findECashAccount = $this->getECashAccountByUser($user);
        if (empty($findECashAccount)) {
            throw new \Exception('ECash Account doesn\'t exists');
        }

        return $this->entityManager->getRepository(SousCompte::class)->findBy([
            'compteBPay' => $findECashAccount,
        ]);
    }
}