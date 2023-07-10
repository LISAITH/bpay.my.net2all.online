<?php

namespace App\Controller;

use App\Entity\UserAuth;
use App\Form\SecretAuthType;
use App\Services\APIService\ApiService;
use App\Services\APIService\ApiUser;
use App\Services\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountController extends BaseController
{
    private $entityManager;
    private $commonService;
    private $securityService;
    private $apiUser;


    public function __construct(SecurityService $securityService, ApiUser $apiService, EntityManagerInterface $entityManager)
    {
        $this->securityService = $securityService;
        $this->apiUser = $apiService;
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/account/secret",methods={"POST","GET"}, name="account_secret")
     *
     * @throws \Exception
     */
    public function secretAuth(Request $request): Response
    {
        $authRequest = $this->check_authentificated($request);
        if (!$authRequest['status']) {
            return $this->redirect($authRequest['url']);
        }
        $connectedUser = $this->apiUser->getUserById($this->getCurrentUser($request)['id']);
        if (empty($connectedUser)) {
            return $this->redirect($authRequest['url']);
        }
        $secretAuthForm = $this->createForm(SecretAuthType::class, null, [
            'method' => 'POST',
            'action' => $this->generateUrl('account_secret'),
        ]);
		
		if(!$this->checkUserAuth($connectedUser)){
		  return $this->redirectToRoute('member_dashboard');
		}

        $secretAuthForm->handleRequest($request);

        if ($secretAuthForm->isSubmitted() && $secretAuthForm->isValid()) {
            $code = sprintf('%s%s%s%s', $secretAuthForm->get('one')->getData(), $secretAuthForm->get('two')->getData(), $secretAuthForm->get('three')->getData(), $secretAuthForm->get('four')->getData());
            $userAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
                'user_id' => $connectedUser->getId(),
            ]);
            if (empty($userAuth)) {
                return $this->createNotFoundException('Not created ressource');
            }
            $userAuth->setIsFirstConnection(false);
            $userAuth->setSecretKey($code);
            $userAuth->setLastConnectionDate(new \DateTime('now'));
            $this->entityManager->persist($userAuth);
            $this->entityManager->flush();

            return $this->redirectToRoute('member_dashboard');
        }

        return $this->render('account/secret.html.twig', [
            'form' => $secretAuthForm->createView(),
        ]);
    }

    protected function checkUserAuth(object $user): bool
    {
        $checkAuth = $this->entityManager->getRepository(UserAuth::class)->findOneBy([
            'user_id' => $user->getId(),
        ]);
        if (empty($checkAuth)) {
            $newAuth = new UserAuth();
            $newAuth->setIsFirstConnection(false);
            $newAuth->setUserId($user->getUserId());
            $newAuth->setLastConnectionDate(new \DateTime('now'));
            $this->entityManager->persist($newAuth);
            $this->entityManager->flush();

            return true;
        }

        return $checkAuth->getIsFirstConnection();
    }
}