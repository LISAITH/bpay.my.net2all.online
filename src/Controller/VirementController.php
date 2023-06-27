<?php

namespace App\Controller;

use App\Form\VirementType;
use App\Service\APIService\ApiUser;
use App\Service\SecurityService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class VirementController extends BaseController
{
    private $securityService;
    private ApiUser $apiUser;

    public function __construct(SecurityService $securityService,ApiUser $apiUser)
    {
        $this->securityService = $securityService;
        $this->apiUser = $apiUser;
    }

    /**
     * @Route("/virement-bpay",methods={"POST","GET"}, name="payment_virement_b_pay")
     */
    public function virementBpay(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $virementForm = $this->createForm(VirementType::class, [], []);
        $virementForm->handleRequest($request);
        if ($virementForm->isSubmitted() && $virementForm->isValid()) {

        }
        return $this->render('virement/virement_ecash_bank.html.twig', [
            'formView' => $virementForm->createView(),
        ]);
    }
}
