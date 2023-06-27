<?php

namespace App\Service\BPay\StripePay;

use App\Entity\BPayEntity\Stripe\StripeCard;
use App\Entity\BPayEntity\Stripe\StripePaymentIntentRequest;
use App\Service\BPayConfigService;
use Stripe\Exception\ApiErrorException;
use Stripe\PaymentMethod;

class StripeService
{
    private $bPayParameter;

    public function __construct(BPayConfigService $payConfigService)
    {
        $this->bPayParameter = $payConfigService;
    }

    public function createStripeClient()
    {
        $stripeParameter = $this->bPayParameter->loadStripeServiceParameter(true);
        $stripe = new \Stripe\StripeClient(
            $stripeParameter->getSecretKey()
        );

        return $stripe;
    }

    public function createPaymentMethod(StripeCard $card): PaymentMethod
    {
        $stripeClient = $this->createStripeClient();

        return $stripeClient->paymentMethods->create([
            'type' => $card->getCardType(),
            'card' => [
                'number' => $card->getCardNumber(),
                'exp_month' => $card->getExpMonth(),
                'exp_year' => $card->getExpYear(),
                'cvc' => $card->getCvc(),
            ],
        ]);
    }

    /**
     * @throws ApiErrorException
     */
    public function retrievePaymentMethod(string $paymentMethodKey): PaymentMethod
    {
        $stripeClient = $this->createStripeClient();

        return $stripeClient->paymentMethods->retrieve($paymentMethodKey, []);
    }

    public function createPaymentIntent(StripePaymentIntentRequest $request, string $customerPaymentMethodId): \Stripe\PaymentIntent
    {
        $stripeClient = $this->createStripeClient();

        try {
            return $stripeClient->paymentIntents->create([
                'amount' => $request->getAmount(),
                'currency' => $request->getCurrency(),
                'automatic_payment_methods' => [
                    'enabled' => $request->getAutomaticEnable(),
                ],
                'payment_method' => $customerPaymentMethodId,
            ]);
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function retrievePaymentIntent(string $paymentMethodId)
    {
        /** @var TYPE_NAME $stripeClient */
        $stripeClient = $this->createStripeClient();

        return $stripeClient->paymentIntents->retrieve(
            $paymentMethodId,
            []
        );
    }

    public function updatePaymentIntent(string $paymentIntentId, object $dataUpdate): bool
    {
        return false;
    }

    public function confirmPaymentIntent(string $paymentIntentId, string $customerPaymentMethodId): \Stripe\PaymentIntent
    {
        $stripeClient = $this->createStripeClient();

        return $stripeClient->paymentIntents->confirm(
            $paymentIntentId,
            ['payment_method' => $customerPaymentMethodId]
        );
    }
}
