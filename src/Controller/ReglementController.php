<?php

namespace App\Controller;

use App\Entity\BPayCreditCardTransaction;
use App\Entity\BPayMomoTransaction;
use App\Entity\BPayProviderItem;
use App\Entity\BPayTransaction;
use App\Entity\ReglementBPayLink;
use App\Form\ReglementType;
use App\Services\APIService\ApiUser;
use App\Services\BPay\StripePay\StripeService;
use App\Services\BusinessService;
use App\Services\CommonService;
use App\Services\QrCodeService;
use App\Services\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class ReglementController extends BaseController
{
    private CommonService $commonService;
    private EntityManagerInterface $entityManager;
    private BusinessService $businessService;
    private QrCodeService $qrCodeService;
    private StripeService $stripeService;
    private ApiUser $apiUser;

    public function __construct(CommonService $commonService, ApiUser $apiUser, StripeService $stripeService, SecurityService $securityService, BusinessService $businessService, QrCodeService $qrCodeService, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->commonService = $commonService;
        $this->qrCodeService = $qrCodeService;
        $this->businessService = $businessService;
        $this->stripeService = $stripeService;
        $this->apiUser = $apiUser;
    }

    #[Route('/generer-lien-paiement', name: 'reglement_generer_lien_paiement')]
    public function genererLienPaiement(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $paymentLinkForm = $this->createForm(ReglementType::class, new ReglementBPayLink(), []);
        $paymentLinkForm->handleRequest($request);

        if ($paymentLinkForm->isSubmitted()) {
            $linkData = $paymentLinkForm->getData();

            $code = $this->commonService->generateBPayLinkReference();

            $dateCreated = new \DateTime('now');
            $expiredDate = $dateCreated->add(new \DateInterval('PT'. 1 .'M'));
            $linkData->setCreateAt($dateCreated);
            $linkData->setExpiredAt($expiredDate);
            $linkData->setIsUsed(false);
            $linkData->setStatus(constants::PENDING);
            $linkData->setCode($code);
            $dataUriQrCode = $this->qrCodeService->generateQrCodeDataUri($code);

            $linkData->setQrCodeImgUrl($dataUriQrCode);
            $uniqueId = Uuid::v4()->toRfc4122();
            $paymentLink = $this->generateUrl('reglement_regler_paiement', ['uniqueId' => $uniqueId], false);
            $linkData->setLinkPaymentUrl($paymentLink);
            $linkData->setUniqueId($uniqueId);
            $linkData->setCurrency(constants::XOF_CURRENCY);
            $linkData->setUserSender($connectedUser->getId());
            $this->entityManager->persist($linkData);
            $this->entityManager->flush();

            return $this->render('reglement/visualiser_lien_paiement.html.twig', [
                'linkData' => $linkData,
            ]);
        }

        return $this->render('reglement/generer_lien.html.twig', [
            'formView' => $paymentLinkForm->createView(),
        ]);
    }

    #[Route('/rejeter-paiement/{uniqueId}', name: 'rejeter_reglement_paiement')]
    public function rejecterReglement(Request $request, string $uniqueId): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $findPayment = $this->entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'uniqueId' => $uniqueId,
        ]);
        if (empty($findPayment) || $findPayment->getIsUsed()) {
            return $this->createNotFoundException('Not Found');
        }
        $findPayment->setIsUsed(true);
        $findPayment->setStatus(constants::FAILED);
        $findPayment->setUpdatedAt(new \DateTime('now'));
        $this->entityManager->persist($findPayment);
        $this->entityManager->flush();

        return $this->render('reglement/_reglement_failed.html.twig', [
        ]);
    }

    #[Route('/regler-paiement/{uniqueId}', name: 'reglement_regler_paiement')]
    public function reglerPaiement(Request $request, string $uniqueId): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect(sprintf('%s/%s', $authRequest['url'], $this->generateUrl('reglement_regler_paiement', ['
           uniqueId' => $uniqueId])));
        }
        $findPayment = $this->entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'uniqueId' => $uniqueId,
        ]);
        /*  if ($findPayment->getUser()->getId() == $connectedUser->getId()) {
              return $this->createNotFoundException('');
          }*/
        if (empty($findPayment) || $findPayment->getIsUsed()) {
            return $this->createNotFoundException('');
        }

        $findSender = $this->apiUser->getUserById($findPayment->getUserSender());
        $sender = $this->apiUser->getUserByProfilV2($findSender);

        return $this->render('reglement/regler_paiement.html.twig', [
            'paimentInfo' => $findPayment,
            'sender' => $sender,
            'senderProfilImg' => $findPayment->getUserSender()->getImageUrl() ?? constants::DEFAULT_IMG_URL,
        ]);
    }

    #[Route('/confirmer-reglement', name: 'reglement_confirmer_reglement')]
    public function confirmerReglement(Request $request): Response
    {
        $findPayment = $this->entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'uniqueId' => $request->get('uniqueId'),
        ]);
        $paymentMethodList = $this->entityManager->getRepository(BPayProviderItem::class)->findBy(['active' => 1], [
            'displayOrder' => 'ASC',
        ]);

        return $this->render('reglement/_confirmer-reglement.html.twig', [
            'paymentMethodList' => $paymentMethodList,
            'reglement' => $findPayment,
            'defaultActive' => $paymentMethodList[0],
        ]);
    }

    #[Route('/valider-reglement', name: 'reglement_valider_reglement')]
    public function validerReglement(Request $request): Response
    {
        $uniqueId = $request->get('uniqueId');
        $transationRef = $request->get('transfertRef') ?? null;
        $paymentMethod = $request->get('paymentMethod') ?? null;
        $amount = $request->get('amount');

        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $findPayment = $this->entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'uniqueId' => $uniqueId,
        ]);

        switch ($paymentMethod) {
            case constants::MOMO_PROVIDER_CODE:
                $findMomoTransaction = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
                    'guid' => $transationRef,
                ]);
                if (empty($findMomoTransaction)) {
                    throw new \Exception('Transaction not found');
                }
                $payeerProfil = $this->apiUser->getUserByProfilV2($connectedUser);

                // La transaction de celui qui demande le regèlement

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($findPayment->getUserSender());
                $bPayTransaction->setTypeOpteration(constants::CREDIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::MOMO_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' par'.$payeerProfil->getNom().' '.$payeerProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));
                $this->entityManager->persist($bPayTransaction);

                // La transaction de celui qui paie pour le règlement

                $senderProfil = $this->apiUser->getUserByProfilV2($findPayment->getUserSender());

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::MOMO_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' à '.$senderProfil->getNom().' '.$senderProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));
                $bPayTransaction->setEntityId($findPayment->getUniqueId());
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setTransactionChangeStatusDate(new \DateTime('now'));
                $this->entityManager->persist($bPayTransaction);

                break;

            case constants::STRIPE_PROVIDER_CODE:
                $this->entityManager->beginTransaction();
                $getPaymentIntent = $this->stripeService->retrievePaymentIntent($transationRef);
                if (empty($getPaymentIntent)) {
                    throw new \Exception('Payment not found');
                }
                $getPaymentMethod = $this->stripeService->retrievePaymentMethod($getPaymentIntent->payment_method);
                if (empty($getPaymentMethod)) {
                    throw new \Exception('Payment not found');
                }

                $uniqueId = Uuid::v4()->toRfc4122();

                $bPayCreditCardPayment = new BPayCreditCardTransaction();
                $bPayCreditCardPayment->setClientSecret($getPaymentIntent->client_secret);
                $bPayCreditCardPayment->setAutomaticPaymentMethods($getPaymentIntent->automatic_payment_methods);
                $bPayCreditCardPayment->setCaptureMethod($getPaymentIntent->capture_method);
                $bPayCreditCardPayment->setConfirmationMethod($getPaymentIntent->confirmation_method);
                $bPayCreditCardPayment->setCreated(\strftime($getPaymentIntent->created));
                $bPayCreditCardPayment->setCurrency($getPaymentIntent->currency);
                $bPayCreditCardPayment->setDescription($getPaymentIntent->description);
                $bPayCreditCardPayment->setReference($getPaymentIntent->id);
                $bPayCreditCardPayment->setObject($getPaymentIntent->object);
                $bPayCreditCardPayment->setPaymentMethod($getPaymentIntent->payment_method);
                $bPayCreditCardPayment->setStatus(\strtoupper($getPaymentIntent->status));
                $bPayCreditCardPayment->setAmount($getPaymentIntent->amount / 100);
                $bPayCreditCardPayment->setCancellationReason($getPaymentIntent->cancellation_reason);
                $bPayCreditCardPayment->setCanceledAt($getPaymentIntent->canceled_at);
                $bPayCreditCardPayment->setGuid($uniqueId);
                $bPayCreditCardPayment->setBrand($getPaymentMethod->card->brand);
                $bPayCreditCardPayment->setCardNumber($getPaymentMethod->card->last4);
                $bPayCreditCardPayment->setExpMonth($getPaymentMethod->card->exp_month);
                $bPayCreditCardPayment->setExpYear($getPaymentMethod->card->exp_year);
                $bPayCreditCardPayment->setCvcCheck($getPaymentMethod->card->checks->cvc_check);
                $bPayCreditCardPayment->setFingerprint($getPaymentMethod->card->fingerprint);
                $bPayCreditCardPayment->setCountry($getPaymentMethod->card->country);
                $bPayCreditCardPayment->setHolderName($getPaymentMethod->billing_details->name);
                $bPayCreditCardPayment->setPaymentMethodType($getPaymentMethod->type);
                $this->entityManager->persist($bPayCreditCardPayment);

                $payeerProfil = $this->apiUser->getUserByProfilV2($connectedUser);

                // La transaction de celui qui demande le règlement

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($findPayment->getUserSender());
                $bPayTransaction->setTypeOpteration(constants::CREDIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::STRIPE_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' par'.$payeerProfil->getNom().' '.$payeerProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));
                $bPayTransaction->setEntityId($findPayment->getUniqueId());
                $this->entityManager->persist($bPayTransaction);

                // La transaction de celui qui paie pour le règlement

                $senderProfil = $this->apiUser->getUserByProfilV2($findPayment->getUserSender());

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::STRIPE_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' à '.$senderProfil->getNom().' '.$senderProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));

                $bPayTransaction->setEntityId($findPayment->getUniqueId());
                $this->entityManager->persist($bPayTransaction);

                $this->entityManager->commit();

                break;
            case constants::BPAY_PROVIDER_CODE:
                $senderECashAccount = $this->businessService->getECashAccountByUser($connectedUser);

                $sender = $this->apiUser->getUserById($findPayment->getUserSender());
                $receiveECashAccount = $this->businessService->getECashAccountByUser($sender);
                $amount = $findPayment->getMontant();
                $finalAmount = 0;
                $frais = 0;
                $this->commonService->calculateOperationPrice(constants::REGLEMENT_BPAY, $finalAmount, $frais);
                if ((int) $amount > $senderECashAccount->getSolde()) {
                    return $this->json([
                        'status' => constants::ERROR,
                        'errorMessage' => 'Solde insuffisant',
                    ], 200);
                }

                // La transaction de celui qui demande le regèlement

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($findPayment->getUserSender());
                $bPayTransaction->setTypeOpteration(constants::CREDIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::BPAY_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' par'.$payeerProfil->getNom().' '.$payeerProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));
                $bPayTransaction->setEntityId($findPayment->getUniqueId());
                $this->entityManager->persist($bPayTransaction);

                // La transaction de celui qui paie pour le règlement

                $bPayTransaction = new BPayTransaction();
                $bPayTransaction->setCurrency(constants::FCFA);
                $bPayTransaction->setTransactionDate(new \DateTime('now'));
                $bPayTransaction->setGuid($uniqueId);
                $bPayTransaction->setStatus(constants::SUCCESSFUL);
                $bPayTransaction->setAmount($amount);
                $bPayTransaction->setUserId($connectedUser->getId());
                $bPayTransaction->setTypeOpteration(constants::DEBIT);
                $bPayTransaction->setTransactionTypeName(constants::REGLEMENT_BPAY);
                $bPayTransaction->setProviderCode(constants::BPAY_PROVIDER_CODE);
                $bPayTransaction->setNote(sprintf('%s%s', 'Règlement de la somme de '.$amount.' '.constants::XOF, ' à '.$senderProfil->getNom().' '.$senderProfil->getPrenoms().' pour '.$findPayment->getPaymentMotif()));
                $bPayTransaction->setEntityId($findPayment->getUniqueId());
                $this->entityManager->persist($bPayTransaction);

                $this->businessService->debitECashAccount($senderECashAccount, (int) $amount);
                $this->businessService->creditECashAccount($receiveECashAccount, (int) $amount);

                break;
        }
        $findPayment->setIsUsed(true);
        $findPayment->setStatus(constants::VALIDATED);
        $findPayment->setUserReceive($connectedUser);

        $this->entityManager->persist($findPayment);
        $this->entityManager->flush();

        return $this->json([
            'status' => constants::SUCCESS,
            'widgetSuccess' => $this->renderView('shared/_reglement_success.html.twig', [
                'amount' => $amount,
            ]),
            'redirectUrl' => $this->generateUrl('member_dashboard'),
        ]);
    }
}