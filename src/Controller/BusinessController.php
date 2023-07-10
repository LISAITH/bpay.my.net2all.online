<?php

namespace App\Controller;

use App\Entity\BPayCreditCardTransaction;
use App\Entity\BPayEntity\Momo\PayerToPay;
use App\Entity\BPayEntity\Momo\RequestToPayData;
use App\Entity\BPayMomoTransaction;
use App\Entity\BPayProviderItem;
use App\Entity\BPayTransaction;
use App\Entity\Recharges;
use App\Form\MomoPayType;
use App\Form\RechargeType;
use App\Services\APIService\ApiUser;
use App\Services\BPay\BPayService;
use App\Services\BPay\MobilePay\MtnMomoService;
use App\Services\BPay\StripePay\StripeService;
use App\Services\BPayConfigService;
use App\Services\BusinessService;
use App\Services\CommonService;
use App\Services\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Uid\Uuid;

class BusinessController extends BaseController
{
    private $businessService;

    private $entityManager;
    private $payService;

    private $securityService;
    private $configService;
    private $commonService;
    private $stripeService;

    private $apiUser;

    public function __construct(BusinessService $businessService, ApiUser $apiUser, StripeService $service, MtnMomoService $mtnMomoService, BPayConfigService $configService, SecurityService $securityService, EntityManagerInterface $entityManager, BPayService $payService, CommonService $commonService)
    {
        $this->entityManager = $entityManager;
        $this->apiUser = $apiUser;
        $this->businessService = $businessService;
        $this->commonService = $commonService;
        $this->stripeService = $service;
        $this->payService = $payService;
        $this->securityService = $securityService;
        $this->configService = $configService;
    }

    /**
     * @Route("/rechargement-compte-bpay",methods={"POST","GET"}, name="business_recharge")
     */
    public function recharger_pay_action(Request $request): Response
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
        $paymentMethodList = $this->entityManager->getRepository(BPayProviderItem::class)->findBy(['active' => 1, 'isInternal' => 0], [
            'displayOrder' => 'ASC',
        ]);

        $paymentMethodDefaultActive = $paymentMethodList[0];
        $rechargeForm = $this->createForm(RechargeType::class, null, [
            'action' => $this->generateUrl('business_recharger_compte_bpay'),
            'method' => 'POST',
            'paymentMethodType' => $paymentMethodDefaultActive->getProviderCode(),
        ]);

        return $this->render('business/recharge.html.twig', [
            'eCashAccount' => $eCashAccount,
            'paymentMethodList' => $paymentMethodList,
            'defaultActive' => $paymentMethodDefaultActive,
            'rechargeForm' => $rechargeForm->createView(),
        ]);
    }

    /**
     * @Route("/recharger-compte-bpay",methods={"POST","GET"}, name="business_recharger_compte_bpay")
     *
     * @throws \Exception
     */
    public function recharge_bpay_account_action(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        $rechargeForm = $this->createForm(RechargeType::class, null, []);
        $rechargeForm->handleRequest($request);

        if ($rechargeForm->isSubmitted() && $rechargeForm->isValid()) {
            $this->entityManager->beginTransaction();

            $amount = $rechargeForm->get('montant')->getData();
            $transationRef = $rechargeForm->get('transactionRef')->getData();
            $providerCode = $rechargeForm->get('paymentMethodType')->getData();
            $findTransaction = null;

            switch ($providerCode) {
                case constants::MOMO_PROVIDER_CODE:
                    $findMomoTransaction = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
                        'guid' => $transationRef,
                    ]);
                    if (empty($findMomoTransaction)) {
                        throw new \Exception('Transaction not found');
                    }
                    $bPayTransaction = new BPayTransaction();
                    $bPayTransaction->setCurrency(constants::FCFA);
                    $bPayTransaction->setTransactionDate(new \DateTime('now'));
                    $bPayTransaction->setGuid($findMomoTransaction->getXReferenceId());
                    $bPayTransaction->setStatus('REQUEST');
                    $bPayTransaction->setAmount($amount);
                    $bPayTransaction->setUserId($connectedUser->getId());
                    $bPayTransaction->setTypeOpteration(constants::CREDIT);
                    $bPayTransaction->setTransactionTypeName(constants::RECHARGEMENT_BPAY);
                    $bPayTransaction->setProviderCode(constants::MOMO_PROVIDER_CODE);
                    $bPayTransaction->setNote('Rechargement de la somme de '.$amount.' '.constants::FCFA);

                    $this->entityManager->persist($bPayTransaction);
                    $this->entityManager->flush();

                    $bPayTransaction->setStatus(constants::SUCCESSFUL);

                    $bPayTransaction->setTransactionChangeStatusDate(new \DateTime('now'));
                    $findTransaction = $bPayTransaction;

                    break;
                case constants::STRIPE_PROVIDER_CODE:
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
                    $this->entityManager->flush();

                    $bPayTransaction = new BPayTransaction();
                    $bPayTransaction->setCurrency(constants::FCFA);
                    $bPayTransaction->setTransactionDate(new \DateTime('now'));
                    $bPayTransaction->setGuid($uniqueId);
                    $bPayTransaction->setStatus(constants::SUCCESSFUL);
                    $bPayTransaction->setAmount($amount);
                    $bPayTransaction->setUserId($connectedUser->getId());
                    $bPayTransaction->setTypeOpteration(constants::CREDIT);
                    $bPayTransaction->setTransactionTypeName(constants::RECHARGEMENT_BPAY);
                    $bPayTransaction->setProviderCode(constants::STRIPE_PROVIDER_CODE);
                    $bPayTransaction->setNote('Rechargement de la somme de '.$amount.' '.constants::FCFA);

                    $this->entityManager->persist($bPayTransaction);
                    $this->entityManager->flush();

                    $findTransaction = $bPayTransaction;
            }

            if (empty($findTransaction)) {
                throw new \Exception('Transaction not found');
            }

            $eCashAccount = $this->businessService->getECashAccountByUser($connectedUser);

            $this->businessService->creditECashAccount($eCashAccount, (int) $amount);

            $recharge = new Recharges();
            $recharge->setDateRecharge(new \DateTime('now'));
            $recharge->setMontant($amount);
            $recharge->setPointVente(null);
            $recharge->setCompteBPay($eCashAccount);

            $this->entityManager->persist($recharge);
            $this->entityManager->flush();

            $findTransaction->setTypeOpteration(constants::CREDIT);
            $findTransaction->setTransactionTypeName(constants::RECHARGEMENT_BPAY);
            $findTransaction->setEntityId($recharge->getId());

            $this->entityManager->persist($recharge);
            $this->entityManager->persist($findTransaction);
            $this->entityManager->flush();

            $this->entityManager->commit();

            $successWidget = $this->renderView('shared/_recharge_pay_success.html.twig', [
                'amount' => $amount,
            ]);

            return new JsonResponse(['status' => 'success',
                'widget' => $successWidget,
                'redirectUrl' => $this->generateUrl('member_dashboard'),
                'amount' => $amount], 200);
        }

        return new JsonResponse([], 200);
    }

    /**
     * @Route("/virement-form",methods={"POST","GET"}, name="business_payment_item_form")
     */
    public function load_payment_form_action(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }

        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);

        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $payData = json_decode($request->get('payData'));

        switch ($payData->providerCode) {
            case constants::MOMO_PROVIDER_CODE:
                $momoForm = $this->createForm(MomoPayType::class, null, [
                    'method' => 'POST',
                    'action' => $this->generateUrl('business_payment_by_momo_form'),
                    'amount' => $payData->amount,
                ]);

                return $this->render('business/_mtn_momo_form.html.twig', [
                    'formView' => $momoForm->createView(),
                    'amount' => $payData->amount,
                    'currency' => constants::FCFA,
                ]);
            case constants::FLOOZ_PROVIDER_CODE:
                $momoForm = $this->createForm(MomoPayType::class, null, [
                    'method' => 'POST',
                    'action' => $this->generateUrl('business_payment_by_momo_form'),
                    'amount' => $payData->amount,
                ]);

                return $this->render('business/_flooz_form.html.twig', [
                    'formView' => $momoForm->createView(),
                    'amount' => $payData->amount,
                    'currency' => constants::FCFA,
                ]);
                break;
            case constants::CELTTIS_PROVIDER_CODE:
                $momoForm = $this->createForm(MomoPayType::class, null, [
                    'method' => 'POST',
                    'action' => $this->generateUrl('business_payment_by_momo_form'),
                    'amount' => $payData->amount,
                ]);

                return $this->render('business/_celtiis_form.html.twig', [
                    'formView' => $momoForm->createView(),
                    'amount' => $payData->amount,
                    'currency' => constants::FCFA,
                ]);
                break;
            case constants::STRIPE_PROVIDER_CODE:
                $stripeConfiguration = $this->configService->loadStripeServiceParameter(true);
                \Stripe\Stripe::setApiKey($stripeConfiguration->getSecretKey());

                $amountConvertedUSD = 0;
                $this->commonService->currencyConverter(constants::FCFA, constants::USD_CURRENCY, (int) $payData->amount, $amountConvertedUSD);

                $paymentIntent = \Stripe\PaymentIntent::create([
                    'amount' => ceil($amountConvertedUSD) * 100,
                    'currency' => \strtolower(constants::USD_CURRENCY),
                    'automatic_payment_methods' => [
                        'enabled' => true,
                    ],
                ]);

                return $this->render('business/_stripe_form.html.twig', [
                    'amount' => $payData->amount,
                    'currency' => constants::FCFA,
                    'clientSecret' => $paymentIntent->client_secret,
                    'publicKey' => $stripeConfiguration->getPublicKey(),
                ]);
            default:
                break;
        }
    }

    /**
     * @Route("/virement-momo-process",methods={"POST","GET"}, name="business_payment_by_momo_form")
     */
    public function paymentByMomo(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $momoForm = $this->createForm(MomoPayType::class, null, [
            'method' => 'POST',
        ]);
        $isFailed = false;
        $momoForm->handleRequest($request);

        if ($momoForm->isSubmitted() && $momoForm->isValid()) {

            $connectedUserV2 = $this->apiUser->getUserByProfilV2($connectedUser);

            $this->entityManager->beginTransaction();
            $amount = $momoForm->get('amount')->getData();
            $phoneNumber = $momoForm->get('number')->getData();
            $isYourAccountValue = $momoForm->get('isYourAccount')->getData();

            $requestToPay = new RequestToPayData();

            $holderName = null;
            if ('true' === $isYourAccountValue) {
                $holderName = sprintf('%s %s', $connectedUserV2->getNom(), $connectedUserV2->getPrenoms());
            } else {
                $holderName = $momoForm->get('holderName')->getData();
            }
            $uniqueId = Uuid::v4()->toRfc4122();
            $sandbox = true;
            $requestToPay->setCurrency($sandbox ? constants::EUR_CURRENCY : constants::FCFA);
            $requestToPay->setAmount($amount ?? 0);
            $requestToPay->setExternalId($uniqueId);
            $requestToPay->setName($holderName);
            $requestToPay->setPayer(new PayerToPay('MSISDN', sprintf('%s%s', '', str_replace(' ', '', $phoneNumber))));
            $requestToPay->setPayeeNote(sprintf('%s', constants::RECHARGE_BPAY_ACCOUNT));
            $requestToPay->setPayerMessage(sprintf('%s', constants::RECHARGE_BPAY_ACCOUNT));

            $payload = $this->payService->bPayRequestPayment(constants::MOMO_PROVIDER_CODE, $requestToPay);

            $findMomo = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
                'xReferenceId' => $uniqueId,
            ]);
            if (empty($findMomo)) {
                throw new \Exception('Transaction error');
            }

            ini_set('max_execution_time', 0);
            $nowPlus6Minute = (new \DateTime('now'))->add(new \DateInterval('PT'. 1 .'M'));
            while (constants::PENDING == $payload->getStatus()) {
                $payload = $this->payService->requestTransactionStatus(constants::MOMO_PROVIDER_CODE, $payload->getRequestId());
                if (new \DateTime('now') > $nowPlus6Minute) {
                    /* $payload->setStatus(constants::FAILED); */
                    $requestToPay->setStatus('FAILED');
                    $this->entityManager->persist($requestToPay);
                    break;
                }
            }

            $this->entityManager->flush();
            $this->entityManager->commit();

            if ($isFailed) {
                return $this->json(['payload' => $payload, 'widgetFail' => $this->renderView('shared/_recharge_pay_failed.html.twig', [
                    'payload' => $payload,
                ])], 200);
            }

            return $this->json(['payload' => $payload], 200);
        }
    }
}