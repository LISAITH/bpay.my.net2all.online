<?php

namespace App\Api\Controller;

use App\Entity\Parametres;
use App\Services\BPayConfigService;
use App\Controller\BaseController;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;


class LoadParametersController extends BaseController
{
    private $configuration;

    public function __construct(BPayConfigService $configService)
    {
        $this->configuration = $configService;
    }

    public function __invoke(Request $request): Response
    {
    
        $loadStripeConfiguration = $this->configuration->loadStripeServiceParameter(true);

        $parameter = new Parametres();
        $parameter->setStripePrivateKey($loadStripeConfiguration->getSecretKey());
        $parameter->setStripePublicKey($loadStripeConfiguration->getPublicKey());
        $parameter->setUuid(Uuid::v4()->toRfc4122());

        return new Response(json_encode($parameter), Response::HTTP_OK, [
            'Content-Type', 'application/json',
        ]);
    }
}