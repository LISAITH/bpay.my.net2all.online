<?php

namespace App\Controller;

use App\Entity\BPayTransaction;
use App\Entity\ReglementBPayLink;
use App\Repository\BanquePartenairesRepository;
use App\Repository\BPayPaymentTransactionRepository;
use App\Services\APIService\ApiUser;
use App\Services\BusinessService;
use App\Services\QrCodeService;
use App\Services\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MemberController extends BaseController
{
    private $businessService;
    private $securityService;
    private $qrCodeService;
    private $entityManager;
    private $apiUser;

    public function __construct(BusinessService $service, ApiUser $apiUser, QrCodeService $qrCodeService, SecurityService $securityService, EntityManagerInterface $entityManager)
    {
        $this->securityService = $securityService;
        $this->entityManager = $entityManager;
        $this->qrCodeService = $qrCodeService;
        $this->businessService = $service;
        $this->apiUser = $apiUser;
    }

    /**
     * @Route("/member",methods={"POST","GET"}, name="member_dashboard")
     */
    public function dashboard_action(Request $request, BanquePartenairesRepository $brepository, BPayPaymentTransactionRepository $repository): Response
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
        // Compte B-Pay de l'utilisateur connecté
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);

        // La liste des sous-comptes de l'utilisateur connecté
        $eCashSubAccounts = $this->businessService->getSubAccountsByUser($connectedUser);
        // La liste des transactions associées au compte de l'utilisateur
        $transactionListCount = $this->businessService->getUserTransactionListCount($connectedUser);

        // La liste des comptes bancaires de l'utilisateur
        $banks = [];

        // La liste des derniers transferts b-pay éffectués
        $lastTransfertList = $this->businessService->getExternalTransferts($connectedUser);

        // La liste des derniers rechargement B-PY éffectués.
        $lastRecharges = $this->businessService->getUserRecharges($connectedUser);


        // Derniers règlements demandés
        $lastReglements = $this->businessService->getUserReglements($connectedUser);

        return $this->render('member/dashboard.html.twig', [
            'eCashAccount' => $eCashAccount,
            'lastRecharges' => $lastRecharges,
            'lastReglements' => $lastReglements,
            'lastTransfertList' => $lastTransfertList,
            'transactions' => $repository->getTransactionHistories($connectedUser),
            'eCashSubAccounts' => $eCashSubAccounts,
            'transactionListCount' => $transactionListCount,
            'banks' => $banks,
        ]);
    }

    /**
     * @Route("/reglement-envoyes",methods={"POST","GET"}, name="member_reglement_envoyes")
     */
    public function reglementEnvoyes(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $reglements = $this->entityManager->getRepository(ReglementBPayLink::class)->findBy([
            'userSender' => $connectedUser,
        ], ['createAt' => 'DESC']);

        return $this->render('member/reglement_envoyes.html.twig', [
            'reglements' => $reglements,
        ]);
    }

    /**
     * @Route("/code-qr",methods={"POST","GET"}, name="member_downloadQrCode")
     */
    public function DownloadQrCode(Request $request, ParameterBagInterface $parameterBag): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);
        $urlImage = $this->qrCodeService->generateQrCodeSaveInPath($eCashAccount->getNumeroCompte(), $eCashAccount->getNumeroCompte(), $parameterBag->get('QRCODEIMG'));

        return new BinaryFileResponse($urlImage);
    }

    /**
     * @Route("/mes-sous-comptes",methods={"POST","GET"}, name="member_sous_comptes")
     */
    public function my_sub_accounts(Request $request): Response
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

        // Récupérer la liste des sous comptes de l'utilisateur connecté.
        $subAccounts = $this->businessService->getSubAccountsByUser($connectedUser);

        return $this->render('member/sub_accounts.html.twig', [
            'subAccounts' => $subAccounts,
        ]);
    }

    /**
     * @Route("/mes-comptes_bancaires",methods={"POST","GET"}, name="member_comptes_bancaires")
     */
    public function my_bank_accounts(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }

        return $this->render('member/my_banks_accounts.html.twig', [
            'banks' => [],
        ]);
    }

    /**
     * @Route("/mes-transactions",methods={"POST","GET"}, name="member_transaction_list")
     *
     * @throws \Exception
     */
    public function transaction_list_action(Request $request): Response
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
        $transactionList = $this->businessService->getUserTransactionList($connectedUser);

        return $this->render('member/transaction_list.html.twig', [
            'transactionList' => $transactionList,
        ]);
    }

    private $transaction;

    /**
     * @Route("/member/menu_left",methods={"POST","GET"}, name="member_menu_left")
     */
    public function render_menu_left_action(Request $request): Response
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

        return $this->render('shared/adminLTE/_side_bar.html.twig', [
             'user' => $this->apiUser->getUserByProfilV2($connectedUser),
             'connetedUser' => $connectedUser,
        ]);
    }

    /**
     * @Route("/member/header",methods={"POST","GET"}, name="member_header")
     */
    public function render_header_action(Request $request): Response
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
        $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);

        return $this->render('shared/_header.html.twig', [
            'user' => $this->apiUser->getUserByProfilV2($connectedUser),
            'eCashAccount' => $eCashAccount,
             'connetedUser' => $connectedUser,
        ]);
    }

    /**
     * @Route("/detail-transaction/{guid}",methods={"POST","GET"}, name="transaction")
     *
     * @throws \Exception
     */
    public function transaction_show(Request $request, string $guid): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);

        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $bPayTransaction = $this->entityManager->getRepository(BPayTransaction::class)->findOneBy([
            'guid' => $guid,
        ]);
        if (empty($bpayTransaction)) {
            $this->createNotFoundException('Transaction not found');
        }
        $transactionData = $this->businessService->convertBPayTransactionToTransactionData($bPayTransaction);

        return $this->render('member/view_transaction.html.twig', [
            'transaction' => $transactionData,
        ]);
    }
}