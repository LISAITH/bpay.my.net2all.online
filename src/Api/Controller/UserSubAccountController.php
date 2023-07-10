<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Services\APIService\ApiUser;
use App\Services\BusinessService;
use App\Services\SecurityService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserSubAccountController extends BaseController
{
    private $businessService;
    private $apiUser;
    private $securityService;

    public function __construct(BusinessService $businessService, ApiUser $apiUser, SecurityService $securityService)
    {
        $this->businessService = $businessService;
        $this->apiUser = $apiUser;
        $this->securityService = $securityService;
    }

    /**
     * @throws \Exception
     */
    public function __invoke(Request $request)
    {
        $userId = $request->attributes->get('user_id');
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'Unkown user.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        // La liste des sous-comptes de l'utilisateur connencté
        return $this->businessService->getSubAccountsByUser($connectedUser);
    }
}