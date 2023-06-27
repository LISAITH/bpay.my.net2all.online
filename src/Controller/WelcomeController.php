<?php

namespace App\Controller;

use App\Service\APIService\ApiUser;
use App\Service\SecurityService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WelcomeController extends BaseController
{
    private ApiUser $apiUser;

    public function __construct(ApiUser $apiUser)
    {
        $this->apiUser = $apiUser;
    }

    /**
     * @Route("/",methods={"POST","GET"}, name="bpay_welcome_action")
     */
    public function welcomeAction(Request $request, SecurityService $securityService): Response
    {
        // $authRequest = $this->check_authentificated($request);
        // if (!$authRequest['status']) {
        //  return $this->redirect($authRequest['url']);
        // }
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }

        return $this->redirectToRoute('member_dashboard');
    }
}
