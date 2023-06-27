<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\CompteBPay;
use App\Service\APIService\ApiUser;
use App\Service\BusinessService;
use App\Service\SecurityService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserBPayAccount extends BaseController
{
    private $businessService;
    private $securityService;
    private $apiAuth;
    private $entityManager;

    public function __construct(BusinessService $businessService, ApiUser $apiUser, SecurityService $securityService, EntityManagerInterface $entityManager)
    {
        $this->businessService = $businessService;
        $this->securityService = $securityService;
        $this->apiAuth = $apiUser;
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/user/{user_id}', name: 'compte', methods: ['GET'])]
    public function getCompte(int $user_id): JsonResponse
    {
        // Utilisez $user_id pour récupérer le compte B-PAY de l'utilisateur spécifié
        // ...
    }

    #[Route('/api/create/compte/Bpay/user', name: 'create_compte_bpay', methods: ['POST'])]
    public function createCompteBpay(Request $request): JsonResponse
    {
		$user_id = $request->get('user_id');
		$type_id = $request->get('type_id');
		
        // Utilisez $user_id et $type_id pour créer un compte B-PAY pour l'utilisateur spécifié
        // Les valeurs possibles pour $type_id sont :
        // 1 - Particulier
        // 2 - Distributeur
        // 3 - Partenaire
        // 4 - Administrateur
        // 5 - Point de vente fixe et mobile
        // 6 - Entreprise

        $accountNumber = $this->getRandomText(10);
        $compteBPay = new CompteBPay();
        $compteBPay->setNumeroCompte($accountNumber);
        $compteBPay->setSolde(0);

        if ($type_id === 1) {
            $compteBPay->setParticulierId($user_id);
        } elseif ($type_id === 2) {
            $compteBPay->setDistributeurId($user_id);
        } elseif ($type_id === 3) {
            $compteBPay->setPartenaireId($user_id);
        } elseif ($type_id === 4) {
            // Pas encore de solution
        } elseif ($type_id === 5) {
            $compteBPay->setPointVenteId($user_id);
        } elseif ($type_id === 6) {
            $compteBPay->setEntrepriseId($user_id);
        }

        $this->entityManager->persist($compteBPay);
        $this->entityManager->flush();

        return $this->json(['message' => 'Compte B-PAY créé avec succès.'], Response::HTTP_CREATED);
    }

    protected function getRandomText(int $length): string
    {
        $characters = '0123456789ABCDEFGHILKMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }
}
