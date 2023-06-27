<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\SousCompte;
use App\Service\APIService\ApiService;
use App\Service\APIService\ApiUser;
use App\Service\BusinessService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetUserSubAccountByService extends BaseController
{
    private EntityManagerInterface $entityManager;
    private ApiService $apiService;
    private ApiUser $apiUser;

    public function __construct(SecurityService $securityService, ApiService $apiService, ApiUser $apiUser,
        BusinessService $service, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->apiService = $apiService;
        $this->apiUser = $apiUser;
    }

    public function __invoke(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        if (empty($userId)) {
            return new JsonResponse(['error' => 'User Id is required'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $serviceId = $request->attributes->get('service_id');
        if (empty($service = $this->apiService->getServiceById($serviceId))) {
            return new JsonResponse(['error' => 'Service not found'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $eCashAccount = $this->apiUser->getUserAccountBPay($connectedUser);
        if (empty($eCashAccount)) {
            return new JsonResponse(['error' => 'Une erreur s\'est produite'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $sousCompte = $this->entityManager->getRepository(SousCompte::class)->findOneBy([
            'service_id' => $service->getId(),
            'compteBPay' => $eCashAccount,
        ]);
        if (empty($sousCompte)) {
            return new JsonResponse(['error' => 'Sous compte introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        return $sousCompte;
    }
}
