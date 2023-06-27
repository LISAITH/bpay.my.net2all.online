<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\BPayTransaction;
use App\Entity\CompteBPay;
use App\Entity\DTO\TransertBPAY;
use App\Entity\SousCompte;
use App\Entity\VirementsBPay;
use App\Service\APIService\ApiUser;
use App\Service\BPayConfigService;
use App\Service\BusinessService;
use App\Service\CommonService;
use App\Utils\constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class TransfertBPAYController extends BaseController
{
    private $entityManager;
    private $commonService;
    private $businessService;
    private $validator;
    private $apiUser;
    private $configuration;

    public function __construct(EntityManagerInterface $entityManager, ApiUser $apiUser, BPayConfigService $configService, ValidatorInterface $validator, CommonService $commonService, BusinessService $businessService)
    {
        $this->entityManager = $entityManager;
        $this->businessService = $businessService;
        $this->commonService = $commonService;
        $this->apiUser = $apiUser;
        $this->configuration = $configService;
        $this->validator = $validator;
    }

    public function __invoke(Request $request, TransertBPAY $data)
    {
        $errors = $this->validator->validate($data);
        if (!empty($returnValues = $this->validateObject($data))) {
            return new Response(json_encode(['error' => $returnValues]), 400, [
                'Content-Type', 'application/json',
            ]);
        }
        $operationList = new ArrayCollection([constants::CP_TO_CP, constants::CP_TO_SC, constants::SC_TO_CP, constants::SC_TO_SC, constants::PCP_TO_SC]);
        if (!$operationList->contains($data->getTypeTransfert())) {
            return new Response(json_encode(['error' => 'Type d\'opération inconnu']), 400, [
                'Content-Type', 'application/json',
            ]);
        }
        switch ($data->getTypeTransfert()) {
            case constants::CP_TO_CP:
                return $this->VirementCompteACompte($data);
            case constants::CP_TO_SC:
                return $this->VirementCompteASousCompte($data);
                break;
            case constants::SC_TO_CP:
                return $this->VirementSousCompteACompte($data);
                break;
            case constants::SC_TO_SC:
                return $this->VirementSousCompteASousCompte($data);
                break;
            case constants::PCP_TO_SC:
                return $this->PaiementBPAY($data);
                break;
            default:
                return new Response(json_encode(['error' => 'Le type d\'opération est inconnu']), 400, [
                    'Content-Type', 'application/json',
                ]);
        }
    }

    private function VirementCompteACompte(TransertBPAY $data): Response
    {
        $this->entityManager->beginTransaction();

        $frais = 0;
        $this->commonService->calculateOperationPrice(constants::FRAIS_INTER_BPAY, $data->getMontant(), $frais);
        $compteBPayEnvoyer = $this->businessService->getECashAccountByNumber($data->getNumeroCompteEnvoyeur());

        if (empty($compteBPayEnvoyer)) {
            $response = new Response(json_encode(['error' => 'Compte Envoyeur introuvable']), 400);
            $response->headers->set('Content-Type', 'application/json');

            return $response;
        }
        $compteBPayReceveur = $this->businessService->getECashAccountByNumber($data->getNumeroCompteReceveur());
        if (empty($compteBPayReceveur)) {
            return new Response(json_encode(['error' => 'Compte Receveur introuvable']), 400,
                ['Content-Type', 'application/json']);
        }
        if ((int) $data->getMontant() > $compteBPayEnvoyer->getSolde() || ($data->getMontant() + $frais) > $compteBPayEnvoyer->getSolde()) {
            return new Response(json_encode(['error' => 'Solde insuffisant']), 400, [
                'Content-Type', 'application/json',
            ]);
        }
        if ($compteBPayEnvoyer->getNumeroCompte() === $compteBPayReceveur->getNumeroCompte()) {
            return new Response(json_encode(['error' => 'Les deux comptes b-pay sont identiques']), 400, [
                'Content-Type', 'application/json',
            ]);
        }

        // Débit Sender CompteBPay & Credit Receive ECompteAccount
        $this->businessService->debitECashAccount($compteBPayEnvoyer, (int) ($data->getMontant() + $frais));
        $this->businessService->creditECashAccount($compteBPayReceveur, (int) $data->getMontant());

        // Create & Save new Virement Ecash
        $newVirementEcash = new VirementsBPay();
        $newVirementEcash->setMontant((int) $data->getMontant());
        $newVirementEcash->setDateTransaction(new \DateTime('now'));
        $newVirementEcash->setIdCompteEnvoyeur($compteBPayEnvoyer);
        $newVirementEcash->setIdCompteReceveur($compteBPayReceveur);

        $this->entityManager->persist($newVirementEcash);
        $this->entityManager->flush();

        $transactionDate = new \DateTime('now');

        //$senderUser = $this->apiUser->getUserByCompteBPay($compteBPayEnvoyer);

        // Create new transaction to sender
        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionChangeStatusDate($transactionDate);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());

        // TODO
        //$bPayTransaction->setUserId()
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($data->getMontant());
        $bPayTransaction->setTypeOpteration(constants::DEBIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        // Create transaction to Receiver
        $bPayTransaction = new BPayTransaction();
        //$receiver = !empty($compteBPayReceveur->getParticulier()) ? $compteBPayReceveur->getParticulier()->getUser() : $compteBPayReceveur->getEntreprise()->getUser();

        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($data->getMontant());
        $bPayTransaction->setTypeOpteration(constants::CREDIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $this->resultatTransfert($data, $frais, $bPayTransaction->getGuid());
    }

    private function VirementCompteASousCompte(TransertBPAY $data): Response
    {
        $amount = $data->getMontant();
        $frais = 0;
        $this->commonService->calculateOperationPrice(constants::FRAIS_BPAY_VERS_SOUS_COMPTE, $amount, $frais);
        $compteBPayEnvoyer = $this->businessService->getECashAccountByNumber($data->getNumeroCompteEnvoyeur());

        if (empty($compteBPayEnvoyer)) {
            return new JsonResponse(['error' => 'Compte Envoyeur introuvable'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $sousCompteBPay = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'numeroSousCompte' => $data->getNumeroCompteReceveur(),
        ]);

        if (empty($sousCompteBPay)) {
            return new JsonResponse(['error' => 'Sous-Compte Receveur introuvable'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $eCashSubAccount = $sousCompteBPay->getCompteBPay();

        if ($compteBPayEnvoyer->getNumeroCompte() != $eCashSubAccount->getNumeroCompte()) {
            return $this->json([
                'error' => 'Le sous-compte spécifié n\'est pas rattaché au compte principal',
            ], 401);
        }

        if ((int) $amount > $compteBPayEnvoyer->getSolde() || ($amount + $frais) > $compteBPayEnvoyer->getSolde()) {
            return new JsonResponse(['error' => 'Recharger votre compte. Votre solde est insuffisant'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $this->entityManager->beginTransaction();

        // Débit Sender CompteBPay & Credit Sender SubAccount Service
        $this->businessService->debitECashAccount($compteBPayEnvoyer, (int) ($amount + $frais));
        $this->businessService->creditUserSubAccount($sousCompteBPay, (int) $amount);

        // Créer une transaction - Virement ECash
        $transactionDate = new \DateTime('now');

        //$senderUser = !empty($compteBPayEnvoyer->getParticulier()) ? $compteBPayEnvoyer->getParticulier()->getUser() : $compteBPayEnvoyer->getEntreprise()->getUser();

        // Create new transaction to sender
        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setTransactionChangeStatusDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::DEBIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(sprintf('%s %s-%s', 'TRANSFERT DE', $amount.' '.constants::XOF_CURRENCY, 'SUR Sous-compte '.$sousCompteBPay->getNumeroSousCompte()));

        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $this->resultatTransfert($data, $frais, $bPayTransaction->getGuid());

    }

    private function VirementSousCompteACompte(TransertBPAY $data): Response
    {
        $amount = $data->getMontant();
        $frais = 0;
        $sousCompteEnvoyer = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'numeroSousCompte' => $data->getNumeroCompteEnvoyeur(),
        ]);
        if (empty($sousCompteEnvoyer)) {
            return new JsonResponse(['error' => 'Sous-compte introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        if ($sousCompteEnvoyer instanceof SousCompte && $amount > $sousCompteEnvoyer->getSolde()) {
            return new JsonResponse(['error' => 'Recharger votre compte. Votre solde est insuffisant'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $this->entityManager->beginTransaction();

        // Crédit B-PAY Principal Account & Débit SubAccount Service
        $this->businessService->creditECashAccount($sousCompteEnvoyer->getCompteBPay(), (int) $amount);
        $this->businessService->debitEcashSubAccount($sousCompteEnvoyer, (int) $amount);

        // Créer une transaction - Virement ECash
        $transactionDate = new \DateTime('now');

        //$senderUser = !empty($sousCompteEnvoyer->getCompteEcash()->getParticulier()) ? $sousCompteEnvoyer->getCompteEcash()->getParticulier()->getUser() : $sousCompteEnvoyer->getCompteEcash()->getEntreprise()->getUser();

        // Create new transaction to sender
        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::DEBIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(sprintf('%s %s-%s', 'TRANSFERT DE', $amount.' '.constants::XOF_CURRENCY, 'SUR Sous-compte '.$sousCompteEnvoyer->getNumeroSousCompte()));

        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $this->resultatTransfert($data, $frais, $bPayTransaction->getGuid());
    }

    private function resultatTransfert(TransertBPAY $data, $frais = 0, $reference = null): Response
    {
        $result = [
             'numeroCompteEnvoyeur' => $data->getNumeroCompteEnvoyeur(),
             'numeroCompteReceveur' => $data->getNumeroCompteReceveur(),
             'montant' => $data->getMontant(),
             'frais' => $frais,
             'motif' => $data->getMotif(),
             'reference' => $reference,
             'devise' => constants::XOF,
         ];

        return new Response(json_encode($result), Response::HTTP_OK, ['Content-Type', 'application/json']);
    }

    private function VirementSousCompteASousCompte(TransertBPAY $data): Response
    {
        $amount = $data->getMontant();
        $frais = 0;
        $sousCompteEnvoyeur = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'numeroSousCompte' => $data->getNumeroCompteEnvoyeur(),
        ]);
        if (empty($sousCompteEnvoyeur)) {
            return new JsonResponse(['error' => 'Sous-compte envoyeur introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $sousCompteReceveur = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'numeroSousCompte' => $data->getNumeroCompteReceveur(),
        ]);
        if (empty($sousCompteReceveur)) {
            return new JsonResponse(['error' => 'Sous-compte receveur introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ((int) $amount > $sousCompteEnvoyeur->getSolde()) {
            return new JsonResponse(['error' => 'Recharger votre compte. Votre solde est insuffisant'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ($sousCompteEnvoyeur->getServiceId() != $sousCompteReceveur->getServiceId()) {
            return new JsonResponse(['error' => 'Les deux sous-comptes ne sont pas du même service'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ($sousCompteEnvoyeur->getNumeroSousCompte() === $sousCompteReceveur->getNumeroSousCompte()) {
            return new JsonResponse(['error' => 'Les deux sous-comptes sont identiques.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $this->entityManager->beginTransaction();

        // Débit Sender CompteBPay & Credit Sender SubAccount Service
        $this->businessService->debitEcashSubAccount($sousCompteEnvoyeur, (int) $amount);
        $this->businessService->creditUserSubAccount($sousCompteReceveur, (int) $amount);

        $transactionDate = new \DateTime('now');
       // $sender = !empty($sousCompteEnvoyeur->getCompteEcash()->getParticulier()) ? $sousCompteEnvoyeur->getCompteEcash()->getParticulier()->getUser() : $sousCompteEnvoyeur->getCompteEcash()->getEntreprise()->getUser();

        // Create new transaction to sender
        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::DEBIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        // Create transaction to Receiver
        $bPayTransaction = new BPayTransaction();
        //$receiver = !empty($sousCompteReceveur->getCompteEcash()->getParticulier()) ? $sousCompteReceveur->getCompteEcash()->getParticulier()->getUser() : $sousCompteReceveur->getCompteEcash()->getEntreprise()->getUser();

        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::CREDIT);
        $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $this->resultatTransfert($data, $frais, $bPayTransaction->getGuid());
    }

    public function PaiementBPAY(TransertBPAY $data): Response
    {
        $amount = $data->getMontant();
        $compteEnvoyeur = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
            'numeroCompte' => $data->getNumeroCompteEnvoyeur(),
        ]);
        if (empty($compteEnvoyeur)) {
            return new JsonResponse(['error' => 'Compte envoyeur introuvable.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ((int) $amount > $compteEnvoyeur->getSolde()) {
            return new JsonResponse(['error' => 'Recharger votre compte. Solde insuffisant'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $sousCompteReceveur = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'numeroSousCompte' => $data->getNumeroCompteReceveur(),
        ]);
        if (empty($sousCompteReceveur)) {
            return new JsonResponse(['error' => 'Sous-Compte receveur introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $this->entityManager->beginTransaction();
        $frais = 0;
        $this->commonService->calculateOperationPrice(constants::REGLEMENT_BPAY, $amount, $frais);
        $finalAmount = $amount + $frais;
        $whoSupportFraisReglement = $this->configuration->getParameter(constants::BPAY_PARAMETER, constants::BPAY_PARAMETER, constants::WHO_SUPPORT_TAXE)->getDefaultValue() ?? '';

        switch ($whoSupportFraisReglement) {
            case 'SENDER':
                $this->businessService->debitECashAccount($compteEnvoyeur, (int) $finalAmount);
                $this->businessService->creditUserSubAccount($sousCompteReceveur, (int) $amount);
                $isSuccess = true;
                break;
            case 'RECEIVER':
                $this->businessService->debitECashAccount($compteEnvoyeur, (int) $amount);
                $this->businessService->creditUserSubAccount($sousCompteReceveur, (int) ($amount - ($finalAmount - $amount)));
                $isSuccess = true;
                break;
            default:
                $this->businessService->debitECashAccount($compteEnvoyeur, (int) $amount);
                $this->businessService->creditUserSubAccount($sousCompteReceveur, (int) $amount);
                $isSuccess = true;
                break;
        }

        $transactionDate = new \DateTime('now');
        //$sender = !empty($compteEnvoyeur->getParticulier()) ? $compteEnvoyeur->getParticulier()->getUser() : $compteEnvoyeur->getEntreprise()->getUser();

        // Create new transaction to sender
        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::DEBIT);
        $bPayTransaction->setTransactionTypeName(constants::REGLEMENT);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::REGLEMENT);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        // Create transaction to Receiver
        $bPayTransaction = new BPayTransaction();
        //$receiver = !empty($sousCompteReceveur->getCompteEcash()->getParticulier()) ? $sousCompteReceveur->getCompteEcash()->getParticulier()->getUser() : $sousCompteReceveur->getCompteEcash()->getEntreprise()->getUser();

        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate($transactionDate);
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setTypeOpteration(constants::CREDIT);
        $bPayTransaction->setTransactionTypeName(constants::REGLEMENT);
        $bPayTransaction->setProviderCode(null);
        $bPayTransaction->setNote(constants::REGLEMENT);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return $this->resultatTransfert($data, $frais, $bPayTransaction->getGuid());
    }

    private function validateObject(TransertBPAY $data): ?string
    {
        $errors = $this->validator->validate($data);
        $errorMessages = null;
        if (count($errors) > 0) {
            foreach ($errors as $error) {
                $errorMessages .= $error->getMessage();
            }
        }

        return $errorMessages;
    }
}
