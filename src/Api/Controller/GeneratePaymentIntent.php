<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\PaiementCarte;
use App\Service\BPayConfigService;
use App\Service\CommonService;
use App\Service\SecurityService;
use App\Utils\constants;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GeneratePaymentIntent extends BaseController
{
    private $configService;
    private $commonService;

    private $securityService;

    public function __construct(BPayConfigService $configService, SecurityService $securityService, CommonService $commonService)
    {
        $this->configService = $configService;
        $this->commonService = $commonService;
        $this->securityService = $securityService;
    }

    public function __invoke(Request $request, PaiementCarte $data)
    {
        $stripeConfiguration = $this->configService->loadStripeServiceParameter(true);
        \Stripe\Stripe::setApiKey($stripeConfiguration->getSecretKey());

        $amountConvertedUSD = 0;
        $this->commonService->currencyConverter(constants::FCFA, constants::USD_CURRENCY, (int) $data->getMontant(), $amountConvertedUSD);

        $paymentIntent = \Stripe\PaymentIntent::create([
            'amount' => ceil($amountConvertedUSD) * 100,
            'currency' => \strtolower(constants::USD_CURRENCY),
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
        ]);

        return new Response(json_encode(['clientSecret' => $paymentIntent->client_secret]), Response::HTTP_OK, ['Content-Type', 'application/json']);
    }
}
