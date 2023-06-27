<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\BPayCreditCardTransaction;
use App\Entity\BPayTransaction;
use App\Service\APIService\ApiUser;
use App\Service\BPay\StripePay\StripeService;
use App\Service\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class PaiementCarteV2Controller extends BaseController
{
    private $stripeService;
    private $entityManager;


    public function __construct(StripeService $stripeService, EntityManagerInterface $entityManager)
    {
        $this->stripeService = $stripeService;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, string $paymentIntentKey)
    {

        $getPaymentIntent = $this->stripeService->retrievePaymentIntent($paymentIntentKey);
        if (empty($getPaymentIntent)) {
            return new JsonResponse(['error' => 'La transaction n\'existe pas'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $getPaymentMethod = $this->stripeService->retrievePaymentMethod($getPaymentIntent->payment_method);

        if (empty($getPaymentMethod)) {
            return new JsonResponse(['error' => 'La transaction n\'existe pas'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
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

        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::USD_CURRENCY);
        $bPayTransaction->setTransactionDate(new \DateTime('now'));
        $bPayTransaction->setGuid($uniqueId);
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($getPaymentIntent->amount / 100);
        $bPayTransaction->setUserId($this->getUser());
        $bPayTransaction->setTypeOpteration(null);
        $bPayTransaction->setTransactionTypeName(constants::PAIEMENT_STRIPE);
        $bPayTransaction->setProviderCode(constants::STRIPE_PROVIDER_CODE);
        $bPayTransaction->setNote($getPaymentIntent->description);

        $this->entityManager->persist($bPayTransaction);

        $this->entityManager->flush();

        return new Response(json_encode(
            [
                'numeroCarte' => $bPayCreditCardPayment->getCardNumber(),
                'brand' => $bPayCreditCardPayment->getBrand(),
                'fingerprint' => $bPayCreditCardPayment->getFingerprint(),
                'payment_method' => $bPayCreditCardPayment->getPaymentMethod(),
                'status' => $bPayCreditCardPayment->getStatus(),
                'holderName' => $bPayCreditCardPayment->getHolderName(),
                'expMonth' => $bPayCreditCardPayment->getExpMonth(),
                'expYear' => $bPayCreditCardPayment->getExpYear(),
            ]
        ), Response::HTTP_OK, [
            'Content-Type', 'application/json',
        ]);
    }
}
