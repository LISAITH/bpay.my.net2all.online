<?php

namespace App\Controller;

use App\Entity\BPayTransaction;
use App\Entity\CompteBPay;
use App\Entity\SousCompte;
use App\Entity\TransfertType;
use App\Entity\User;
use App\Entity\UserAuth;
use App\Entity\VirementsBPay;
use App\Form\TransferForms\CPToCPType;
use App\Form\TransferForms\CPToSCType;
use App\Form\TransferForms\SCToCPType;
use App\Form\TransferForms\SCToSCType;
use App\Form\TransferForms\TransfertVersSousCompteType;
use App\Service\APIService\ApiService;
use App\Service\APIService\ApiUser;
use App\Service\BPay\StripePay\StripeService;
use App\Service\BPayConfigService;
use App\Service\BusinessService;
use App\Service\CommonService;
use App\Service\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class TransactionController extends BaseController
{
    private $stripeService;
    private $businessService;
    private $entityManager;
    private $securityService;
    private $commonService;
    private $bpayConfigService;

    private ApiUser $apiUser;

    private ApiService $apiService;

    public function __construct(StripeService $service, ApiUser $apiUser, ApiService $apiService, CommonService $commonService, SecurityService $securityService, BPayConfigService $configService, BusinessService $businessService, EntityManagerInterface $entityManager)
    {
        $this->stripeService = $service;
        $this->entityManager = $entityManager;
        $this->commonService = $commonService;
        $this->securityService = $securityService;
        $this->businessService = $businessService;
        $this->apiUser = $apiUser;
        $this->apiService = $apiService;
        $this->bpayConfigService = $configService;
    }

    /**
     * @Route("/transfer-bpay/{type?}/{scId?}",methods={"POST","GET"}, name="transaction_transfert_bpay")
     */
    public function transferBPay(Request $request, ?string $type, ?int $scId): Response
    {
        /* $authRequest = $this->check_authentificated($request);
       if (!$authRequest['status']) {
           return $this->redirect($authRequest['url']);
       }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        // Récupérer la liste de tous les types de transfert
        $transfertTypeList = TransfertType::loadTransfertTypeList();
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);

        return $this->render('transaction/transfer.html.twig', [
            'eCashAccount' => $eCashAccount,
            'transfertList' => $transfertTypeList,
            'type' => $type ?? '',
        ]);
    }

    /**
     * @Route("/transfert-vers-sous-compte/{id}",methods={"POST","GET"}, name="transaction_transfert_entre_sous_compte")
     */
    public function TransfertVersSousCompte(Request $request, int $id): Response
    {
        /* $authRequest = $this->check_authentificated($request);
     if (!$authRequest['status']) {
         return $this->redirect($authRequest['url']);
     }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $findSousCompte = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'id' => $id,
        ]);
        if (empty($findSousCompte)) {
            return $this->createNotFoundException('Not Found');
        }
        $form = $this->createForm(TransfertVersSousCompteType::class, [], [
            'subAccountId' => $findSousCompte->getId(),
        ]);

        return $this->render('transaction/transfert_vers_sous_compte.html.twig', [
            'formView' => $form->createView(),
            'compte' => $findSousCompte,
        ]);
    }

    /**
     * @Route("/make-sub-account-transfer",methods={"POST","GET"}, name="transaction_make_sub_account_transfert")
     */
    public function MakeTransfertSubAccountToSubAccount(Request $request): Response
    {
        $serviceId = $request->get('serviceId');
        $receiveEmail = $request->get('email');
        $amount = (int) $request->get('amount');
        $step = $request->get('step');
        $frais = 0;

        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $receiver = $this->apiUser->getUserByEmailAddress(trim($receiveEmail));
        if (empty($connectedUser) || empty($receiver)) {
            throw new \Exception('User not found');
        }
        $service = $this->apiService->getServiceById($serviceId);
        if (empty($service)) {
            throw new \Exception('Service not found');
        }
        $receiveSubAccount = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'service_id' => $serviceId,
            'compteBPay' => $this->businessService->getECashAccountByUser($receiver),
        ]);
        $senderSubAccount = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'service_id' => $serviceId,
            'compteBPay' => $this->businessService->getECashAccountByUser($connectedUser),
        ]);
        if (empty($senderSubAccount) || empty($receiveSubAccount)) {
            throw new \Exception('Unable to find sub account');
        }
        switch ($step) {
            case 1:
                if ((int) $amount > $senderSubAccount->getSolde() || ($amount + $frais) > $senderSubAccount->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }
                if ((int) $amount <=  0) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le montant doit être supérieur à zéro',
                    ], 200);
                }

                return $this->json([
                    'status' => constants::SUCCESS,
                    'step' => $step++,
                    'widgetConfirm' => $this->renderView('transaction/_confirm_transfert_vers_sous_compte.html.twig', [
                        'amount' => $amount,
                        'service' => $service,
                        'receiveAccount' => $this->apiUser->getUserByProfilV2($receiver),
                        'sender' => $this->apiUser->getUserByProfilV2($connectedUser),
                        'currency' => constants::FCFA,
                        'eCashAccount' => $receiveSubAccount,
                        'receiverImgProfil' => $service->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'senderECashAccount' => $senderSubAccount,
                        'senderProfilImg' => $service->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'step' => $step++,
                    ]),
                ], 200);
                break;
            case 2:
                $this->entityManager->beginTransaction();
                // Check Code Confirmation
                $codeConfirmation = (string) $request->get('code');
                $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                    'user_id' => $connectedUser->getId(),
                ]);
                if (empty($checkAuth) || $checkAuth instanceof UserAuth && (int) $checkAuth->getSecretKey() != (int) $codeConfirmation) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le code n\'est pas valide',
                    ], 200);
                }
                // Débit Sender CompteBPay & Credit Sender SubAccount Service
                $this->businessService->debitEcashSubAccount($senderSubAccount, (int) $amount);
                $this->businessService->creditUserSubAccount($receiveSubAccount, (int) $amount);

                $transactionDate = new \DateTime('now');

                // Create new transaction to sender
                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                // Create transaction to Receiver
                $bPayTransaction = new BPayTransaction();
                $receiver = $this->apiUser->getUserByCompteBPay($receiveSubAccount->getCompteEcash());

                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($receiver->getId());
                $bPayTransaction->setTypeOpteration(constants::CREDIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote(constants::TRANSFERT_BPAY);
                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                $this->entityManager->commit();
                $this->addFlash('transactionSuccess', 'Votre transaction a été effectuée avec succès');

                return $this->json([
                    'status' => constants::SUCCESS,
                    'widgetSuccess' => $this->renderView('shared/_transfert_success.html.twig', [
                        'amount' => $amount,
                    ]),
                    'redirectUrl' => $this->generateUrl('member_dashboard'),
                ]);
        }
    }

    /**
     * @Route("/verify-user",methods={"POST","GET"}, name="transaction_verify_user")
     */
    public function checkUserExist(Request $request)
    {
        $value = $request->get('value');
        try {
            if (empty($value)) {
                throw new \Exception('Email is not valid');
            }
            $userFind = null;
            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                throw new \Exception('Email is not valid');
            }
            $userFind = $this->apiUser->getUserByEmailAddress(trim($value));
            if (empty($userFind)) {
                throw new \Exception('Utilisateur inconnu');
            }
        } catch (\Exception $e) {
            return $this->json([
                'error' => $e->getMessage(),
                'status' => constants::ERROR,
            ]);
        }

        return $this->json([
            'user' => $this->apiUser->getUserByProfilV2($userFind),
            'status' => constants::SUCCESS,
        ]);
    }

    /**
     * @Route("/transfer-compte-a-compte",methods={"POST","GET"}, name="transaction_compte_a_compte")
     */
    public function AccountToAccountTransfer(Request $request): Response
    {
        /* $authRequest = $this->check_authentificated($request);
      if (!$authRequest['status']) {
          return $this->redirect($authRequest['url']);
      }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $accountNumber = $request->get('accountNumber');
        $amount = (int) $request->get('amount');
        $step = (int) $request->get('step') ?? 0;
        $frais = 0;

        $this->commonService->calculateOperationPrice(constants::FRAIS_INTER_BPAY, $amount, $frais, 0);

        switch ($step) {
            case 1:
                $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);
                if ((int) $amount > $eCashAccount->getSolde() || ($amount + $frais) > $eCashAccount->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }
                if ((int) $amount <=  0) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le montant doit être supérieur à zéro',
                    ], 200);
                }
                $accountBPAY = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                    'numeroCompte' => trim($accountNumber),
                ]);
                if (empty($accountBPAY)) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Ce compte n\'existe pas!',
                    ], 200);
                }
                if (strtoupper(trim($eCashAccount->getNumeroCompte())) === strtoupper(trim($accountBPAY->getNumeroCompte()))) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le compte destinataire est celui de votre compte principal. Transaction impossible!',
                    ], 200);
                }
                $receiver = $this->apiUser->getUserByCompteBPay($accountBPAY);
                $receiverImgProfil = null ?? constants::DEFAULT_IMG_URL;

                if (empty($receiver)) {
                    throw new \Exception('Receive not found');
                }

                // Generate confirmation Transfert Code
                $codeConfirmation = $this->commonService->generateValidationCode();
               
                return $this->json([
                    'status' => constants::SUCCESS,
                    'step' => $step++,
                    'widgetConfirm' => $this->renderView('transaction/_confirm_cp_to_cp.html.twig', [
                        'receiver' => $receiver,
                        'frais' => $frais,
                        'connetedUser' => $connectedUser,
                        'receiverImgProfil' => $receiverImgProfil,
                        'eCashAccount' => $accountBPAY,
                        'senderECashAccount' => $eCashAccount,
                        'senderProfilImg' => $connectedUser->getImageUrl() ?? constants::DEFAULT_IMG_URL,
                        'amount' => $amount,
                        'currency' => constants::FCFA,
                        'step' => $step++,
                    ]),
                ], 200);
                break;
            case 2:
                // Check Code Confirmation
                $codeConfirmation = (string) $request->get('codeConfirmation');
                $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                    'user_id' => $connectedUser->getId(),
                ]);
                if ($checkAuth instanceof UserAuth && (int) $checkAuth->getSecretKey() != (int) $codeConfirmation) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le code n\'est pas valide',
                    ], 200);
                }

                $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);
                $accountBPAY = $this->entityManager->getRepository(CompteBPay::class)->findOneBy([
                    'numeroCompte' => trim($accountNumber),
                ]);

                $this->entityManager->beginTransaction();
                // Créer un virement - Virement ECash

                // Débit Sender CompteBPay & Credit Receive ECompteAccount
                $this->businessService->debitECashAccount($eCashAccount, (int) ($amount + $frais));
                $this->businessService->creditECashAccount($accountBPAY, (int) $amount);

                // Create & Save new Virement Ecash
                $newVirementEcash = new VirementsBPay();
                $newVirementEcash->setMontant((int) $amount);
                $newVirementEcash->setDateTransaction(new \DateTime('now'));
                $newVirementEcash->setIdCompteEnvoyeur($eCashAccount);
                $newVirementEcash->setIdCompteReceveur($accountBPAY);

                $this->entityManager->persist($newVirementEcash);
                $this->entityManager->flush();

                $transactionDate = new \DateTime('now');

                // Create new transaction to sender
                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote('Transfert de la somme de '.$amount.' '.constants::FCFA.' vers compte nº'.$accountNumber);

                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                // Create transaction to Receiver
                $bPayTransaction = new BPayTransaction();
                $receiver = $this->apiUser->getUserByCompteBPay($accountBPAY);

                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($receiver->getId());
                $bPayTransaction->setTypeOpteration(constants::CREDIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote('Réception de la somme de '.$amount.' '.constants::FCFA.' Compte nº '.$eCashAccount->getNumeroCompte());

                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                $this->entityManager->commit();
                $this->addFlash('transactionSuccess', 'Votre transaction a été effectuée avec succès');

                return $this->json([
                    'status' => constants::SUCCESS,
                    'widgetSuccess' => $this->renderView('shared/_transfert_success.html.twig', [
                        'amount' => $amount,
                    ]),
                    'redirectUrl' => $this->generateUrl('member_dashboard'),
                ]);
                break;
            default:
                break;
        }
    }

    /**
     * @Route("/transfer-compte-a-souscompte",methods={"POST","GET"}, name="transaction_compte_a_souscompte")
     */
    public function AccountToSubAccountTransfer(Request $request): Response
    {
        /* $authRequest = $this->check_authentificated($request);
       if (!$authRequest['status']) {
           return $this->redirect($authRequest['url']);
       }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $accountNumber = $request->get('accountNumber');
        $amount = (int) $request->get('amount');
        $step = (int) $request->get('step') ?? 0;
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);
        $frais = 0;

        $this->commonService->calculateOperationPrice(constants::FRAIS_BPAY_VERS_SOUS_COMPTE, $amount, $frais);

        switch ($step) {
            case 1:
                if ((int) $amount > $eCashAccount->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }
                if ((int) $amount <=  0) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le montant doit être supérieur à zéro',
                    ], 200);
                }
                $accountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $accountNumber,
                ]);
                if (empty($accountBPAY)) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Ce compte n\'existe pas!',
                    ], 200);
                }

                $eCashSubAccount = $accountBPAY->getCompteBPay();
                if ($eCashAccount->getNumeroCompte() != $eCashSubAccount->getNumeroCompte()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Ce sous-compte destinataire n\'est pas rattaché à votre compte B-PAY',
                    ], 200);
                }
                $service = $this->apiService->getServiceById($accountBPAY->getServiceId());

                return $this->json([
                    'status' => constants::SUCCESS,
                    'step' => $step++,
                    'frais' => $frais,
                    'widgetConfirm' => $this->renderView('transaction/_confirm_cp_to_sc.html.twig', [
                        'amount' => $amount,
                        'currency' => constants::FCFA,
                        'eCashAccount' => $accountBPAY,
                        'frais' => $frais,
                        'receiverImgProfil' => $service->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'senderECashAccount' => $eCashAccount,
                        'senderProfilImg' => $connectedUser->getImageUrl() ?? constants::DEFAULT_IMG_URL,
                        'step' => $step++,
                    ]),
                ], 200);
            case 2:
                // Check Code Confirmation
               /* $codeConfirmation = (string) $request->get('codeConfirmation');
                $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                    'user_id' => $connectedUser->getId(),
                ]);
                if (empty($checkAuth) || $checkAuth instanceof UserAuth && (int) $checkAuth->getSecretKey() != (int) $codeConfirmation) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le code n\'est pas valide',
                    ], 200);
                }*/

                $accountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $accountNumber,
                ]);

                $this->entityManager->beginTransaction();

                // Débit Sender CompteBPay & Credit Sender SubAccount Service
                $this->businessService->debitECashAccount($eCashAccount, (int) ($amount + $frais));
                $this->businessService->creditUserSubAccount($accountBPAY, (int) $amount);

                // Créer une transaction - Virement ECash
                $transactionDate = new \DateTime('now');

                // Create new transaction to sender
                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote('Transfert de la somme '.$amount.' '.constants::FCFA.' vers sous compte nº'.$accountBPAY->getNumeroSousCompte());

                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                $this->entityManager->commit();

                $this->addFlash('transactionSuccess', 'Votre transaction a été effectuée avec succès');

                return $this->json([
                    'status' => constants::SUCCESS,
                    'widgetSuccess' => $this->renderView('shared/_transfert_success.html.twig', [
                        'amount' => $amount,
                    ]),
                    'redirectUrl' => $this->generateUrl('member_dashboard'),
                ]);
            default:
                return $this->createNotFoundException('Not Found');
        }
    }

    /**
     * @Route("/transfer-souscompte-a-compte",methods={"POST","GET"}, name="transaction_souscompte_a_compte")
     */
    public function SubAccountToAccountTransfer(Request $request): Response
    {
        /* $authRequest = $this->check_authentificated($request);
       if (!$authRequest['status']) {
           return $this->redirect($authRequest['url']);
       }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $accountNumber = $request->get('accountNumber');
        $amount = (int) $request->get('amount');
        $step = (int) $request->get('step') ?? 0;

        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);
        switch ($step) {
            case 1:
                $accountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $accountNumber,
                ]);
                if (empty($accountBPAY)) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Ce compte n\'existe pas!',
                    ], 200);
                }

                if ($accountBPAY instanceof SousCompte && $amount > $accountBPAY->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }
                if ((int) $amount <=  0) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le montant doit être supérieur à zéro',
                    ], 200);
                }

                $service = $this->apiService->getServiceById($accountBPAY->getServiceId());

                return $this->json([
                    'status' => constants::SUCCESS,
                    'step' => $step++,
                    'widgetConfirm' => $this->renderView('transaction/_confirm_sc_to_cp.html.twig', [
                        'amount' => $amount,
                        'currency' => constants::FCFA,
                        'eCashAccount' => $accountBPAY,
                        'receiverImgProfil' => $service->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'senderECashAccount' => $eCashAccount,
                        'senderProfilImg' => $connectedUser->getImageUrl() ?? constants::DEFAULT_IMG_URL,
                        'step' => $step++,
                    ]),
                ], 200);
            case 2:
              /*  // Check Code Confirmation
                $codeConfirmation = (string) $request->get('codeConfirmation');
                $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                    'user_id' => $connectedUser->getId(),
                ]);
                if (empty($checkAuth) || $checkAuth instanceof UserAuth && (int) $checkAuth->getSecretKey() != (int) $codeConfirmation) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le code n\'est pas valide',
                    ], 200);
                }*/

                $accountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $accountNumber,
                ]);

                $this->entityManager->beginTransaction();

                // Crédit B-PAY Principal Account & Débit SubAccount Service
                $this->businessService->creditECashAccount($eCashAccount, (int) $amount);
                $this->businessService->debitEcashSubAccount($accountBPAY, (int) $amount);

                // Créer une transaction - Virement ECash
                $transactionDate = new \DateTime('now');

                // Create new transaction to sender
                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate($transactionDate);
                $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                $bPayTransaction->setProviderCode(null);
                $bPayTransaction->setNote('Transfert de la somme de '.$amount.' '.constants::FCFA.' du sous compte nº '.$accountBPAY->getNumeroSousCompte().'vers compte principal nº '.$eCashAccount->getNumeroCompte());

                $this->entityManager->persist($bPayTransaction);
                $this->entityManager->flush();

                $this->entityManager->commit();

                $this->addFlash('transactionSuccess', 'Votre transaction a été effectuée avec succès');

                return $this->json([
                    'status' => constants::SUCCESS,
                    'widgetSuccess' => $this->renderView('shared/_transfert_success.html.twig', [
                        'amount' => $amount,
                    ]),
                    'redirectUrl' => $this->generateUrl('member_dashboard'),
                ]);
        }
    }

    /**
     * @Route("/transfer-souscompte-a-souscompte",methods={"POST","GET"}, name="transaction_souscompte_a_souscompte")
     */
    public function SubAccountToSubAccount(Request $request): Response
    {
        /* $authRequest = $this->check_authentificated($request);
      if (!$authRequest['status']) {
          return $this->redirect($authRequest['url']);
      }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $accountNumber = $request->get('accountNumber');
        $receiveAccountNumber = $request->get('receiveAccountNumber');
        $amount = (int) $request->get('amount');
        $step = (int) $request->get('step') ?? 0;

        switch ($step) {
            case 1:
                $accountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $accountNumber,
                ]);
                if (empty($accountBPAY)) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le sous compte à débiter n\'existe pas!',
                    ], 200);
                }
                $receiveAccountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                    'numeroSousCompte' => $receiveAccountNumber,
                ]);
                if (empty($receiveAccountBPAY)) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le sous compte à créditer n\'existe pas!',
                    ], 200);
                }
                if ((int) $amount > $accountBPAY->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }
                if ((int) $amount <=  0) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le montant doit être supérieur à zéro',
                    ], 200);
                }

                $senderService = $this->apiService->getServiceById($accountBPAY->getServiceId());
                $receiveService = $this->apiService->getServiceById($receiveAccountBPAY->getServiceId());

                if ($senderService->getId() != $receiveService->getId()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Les deux sous-comptes ne sont pas du même service',
                    ], 200);
                }

                if ($accountBPAY->getNumeroSousCompte() === $receiveAccountBPAY->getNumeroSousCompte()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Les deux sous-comptes sont identiques. Transfert impossible',
                    ], 200);
                }

                return $this->json([
                    'status' => constants::SUCCESS,
                    'step' => $step++,
                    'widgetConfirm' => $this->renderView('transaction/_confirm_sc_to_sc.html.twig', [
                        'amount' => $amount,
                        'currency' => constants::FCFA,
                        'eCashAccount' => $receiveAccountBPAY,
                        'receiverImgProfil' => $senderService->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'senderECashAccount' => $accountBPAY,
                        'senderProfilImg' => $senderService->getLogo() ?? constants::DEFAULT_IMG_URL,
                        'step' => $step++,
                    ]),
                ], 200);
            case 2:
                // Check Code Confirmation
                $codeConfirmation = (string) $request->get('codeConfirmation');
                $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                    'user_id' => $connectedUser->getId(),
                ]);
                if (empty($checkAuth) || $checkAuth instanceof UserAuth && (int) $checkAuth->getSecretKey() != (int) $codeConfirmation) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Le code n\'est pas valide',
                    ], 200);
                }

                try {
                    $senderAccountBPay = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                        'numeroSousCompte' => $accountNumber,
                    ]);
                    $receiveAccountBPAY = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
                        'numeroSousCompte' => $receiveAccountNumber,
                    ]);
                    $this->entityManager->beginTransaction();

                    // Débit Sender CompteBPay & Credit Sender SubAccount Service
                    $this->businessService->debitEcashSubAccount($senderAccountBPay, (int) $amount);
                    $this->businessService->creditUserSubAccount($receiveAccountBPAY, (int) $amount);

                    $transactionDate = new \DateTime('now');

                    // Create new transaction to sender
                    $bPayTransaction = new BPayTransaction();
                    $bPayTransaction->setCurrency(constants::FCFA);
                    $bPayTransaction->setTransactionDate($transactionDate);
                    $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                    $bPayTransaction->setStatus(constants::SUCCESSFUL);
                    $bPayTransaction->setAmount($amount);
                    $bPayTransaction->setUserId($connectedUser->getId());
                    $bPayTransaction->setTypeOpteration(constants::DEBIT);
                    $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                    $bPayTransaction->setProviderCode(null);
                    $bPayTransaction->setNote('Transfert de la somme de '.$amount.' '.constants::FCFA.' du sous compte nº '.$senderAccountBPay->getNumeroSousCompte().'vers sous compte nº '.$receiveAccountBPAY->getNumeroSousCompte());

                    $this->entityManager->persist($bPayTransaction);
                    $this->entityManager->flush();

                    // Create transaction to Receiver
                    $bPayTransaction = new BPayTransaction();
                    $receiver = $this->apiUser->getUserByCompteBPay($receiveAccountBPAY);

                    $bPayTransaction->setCurrency(constants::FCFA);
                    $bPayTransaction->setTransactionDate($transactionDate);
                    $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
                    $bPayTransaction->setStatus(constants::SUCCESSFUL);
                    $bPayTransaction->setAmount($amount);
                    $bPayTransaction->setUserId($receiver->getId());
                    $bPayTransaction->setTypeOpteration(constants::CREDIT);
                    $bPayTransaction->setTransactionTypeName(constants::TRANSFERT_BPAY);
                    $bPayTransaction->setProviderCode(null);
                    $bPayTransaction->setNote('Réception de la somme '.$amount.' '.constants::FCFA.' du sous compte nº '.$senderAccountBPay->getNumeroSousCompte().'vers sous compte nº '.$receiveAccountBPAY->getNumeroSousCompte());

                    $this->entityManager->persist($bPayTransaction);
                    $this->entityManager->flush();

                    $this->entityManager->commit();
                    $this->addFlash('transactionSuccess', 'Votre transaction a été effectuée avec succès');

                    return $this->json([
                        'status' => constants::SUCCESS,
                        'widgetSuccess' => $this->renderView('shared/_transfert_success.html.twig', [
                            'amount' => $amount,
                        ]),
                        'redirectUrl' => $this->generateUrl('member_dashboard'),
                    ]);
                } catch (\Exception $e) {
                    throw $e;
                }
            default:
                return $this->createNotFoundException('Not Found');
        }
    }

    /**
     * @Route("/transfer-bpay-form-type",methods={"POST","GET"}, name="transaction_bpay_form_type")
     */
    public function loadBPayTransferFormByType(Request $request): Response
    {
        /* $authRequest = $this->check_authentificated($request);
       if (!$authRequest['status']) {
           return $this->redirect($authRequest['url']);
       }*/
        // $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $type = $request->get('type');
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);

        switch ($type) {
            case constants::CP_TO_CP:
                $formView = $this->createForm(CPToCPType::class, null, []);

                return $this->render('transaction/_cp_to_cp.html.twig', [
                    'formView' => $formView->createView(),
                ]);
                break;
            case constants::CP_TO_SC:
                $formView = $this->createForm(CPToSCType::class, null,
                    ['eCash' => $eCashAccount]);

                return $this->render('transaction/_cp_to_sc.html.twig', [
                    'formView' => $formView->createView(),
                    'eCash' => $eCashAccount,
                ]);
                break;
            case constants::SC_TO_CP:
                $formView = $this->createForm(SCToCPType::class, null,
                    ['eCash' => $eCashAccount]);

                return $this->render('transaction/_sc_to_cp.html.twig', [
                    'formView' => $formView->createView(),
                    'eCash' => $eCashAccount,
                ]);
            case constants::SC_TO_SC:
                $formView = $this->createForm(SCToSCType::class, null,
                    ['eCash' => $eCashAccount]);

                return $this->render('transaction/_sc_to_sc.html.twig', [
                    'formView' => $formView->createView(),
                    'eCash' => $eCashAccount,
                ]);
            default:
                break;
        }
    }
}
