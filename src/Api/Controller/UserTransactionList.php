<?php

namespace App\Api\Controller;

use App\Services\APIService\ApiUser;
use App\Services\BusinessService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use App\Controller\BaseController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use App\Services\SecurityService;

class UserTransactionList extends BaseController
{
    private $businessService;
    private $securityService;
    private $apiUser;

    public function __construct(BusinessService $businessService,ApiUser $apiUser, SecurityService $securityService)
    {
        $this->businessService = $businessService;
        $this->apiUser = $apiUser;
         $this->securityService = $securityService;
    }

    public function __invoke(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'Unkown user.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        // La liste des transactions associées au compte de l'utilisateur
        return $this->businessService->getUserTransactionList($connectedUser);
    }
}