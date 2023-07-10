<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\BPayMomoTransaction;
use App\Services\BusinessService;
use App\Services\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetTransactionController extends BaseController
{
    private $businessService;
    private $entityManager;


    public function __construct(EntityManagerInterface $entityManager, SecurityService $securityService, BusinessService $service)
    {
        $this->entityManager = $entityManager;
        $this->businessService = $service;
    }

    public function __invoke(Request $request)
    {/*
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return new JsonResponse(['error' => 'This action requires authentication.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $connectedUser = $this->securityService->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'This action requires authentication.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }*/
        $transactionId = (int) $request->get('Id');
        $bPayTransaction = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
            'id' => $transactionId,
        ]);
        if (empty($bPayTransaction)) {
            return new JsonResponse(['error' => 'HTTP_FORBIDDEN'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        return $this->businessService->convertBPayTransactionToTransactionData($bPayTransaction);
    }
}