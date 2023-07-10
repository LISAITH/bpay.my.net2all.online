<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\CompteBPay;
use App\Repository\CompteBPayRepository;
use App\Services\APIService\ApiUser;
use App\Services\BusinessService;
use App\Services\SecurityService;
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
    private CompteBPayRepository $compteBPayRepository;

    public function __construct(CompteBPayRepository $compteBPayRepository, BusinessService $businessService, ApiUser $apiUser, SecurityService $securityService, EntityManagerInterface $entityManager)
    {
        $this->businessService = $businessService;
        $this->securityService = $securityService;
        $this->apiAuth = $apiUser;
        $this->entityManager = $entityManager;
        $this->compteBPayRepository = $compteBPayRepository;
    }

    #[Route('/one/compte/bpay/{user_id}/{type_id}', name: 'get_one_compte_bpay', methods: ['GET'])]
    public function getOneCompteBpay(int $user_id, int $type_id): JsonResponse
    {
        if ($type_id === 1) {
            $compte = $this->compteBPayRepository->findOneBy(["particulier_id" => $user_id]);
        } elseif ($type_id === 2) {
            $compte = $this->compteBPayRepository->findOneBy(["distributeur_id" => $user_id]);
        } elseif ($type_id === 3) {            
            $compte = $this->compteBPayRepository->findOneBy(["partenaire_id" => $user_id]);
        } elseif ($type_id === 4) {
            // Pas encore de solution
        } elseif ($type_id === 5) {
            $compte = $this->compteBPayRepository->findOneBy(["pointVente_id" => $user_id]);
        } elseif ($type_id === 6) {
            $compte = $this->compteBPayRepository->findOneBy(["entreprise_id" => $user_id]);
        }

        return $this->json($compte);
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
        // 7 - Plateforme

        $accountNumber = $this->getRandomText(10);
        $compteBPay = new CompteBPay();
        $compteBPay->setNumeroCompte($accountNumber);
        $compteBPay->setSolde(0);

        if ($type_id === 1) {
            $ecash->setParticulierId($user_id);
        } elseif ($type_id === 2) {
            $ecash->setDistributeurId($user_id);
        } elseif ($type_id === 3) {
            $ecash->setPartenaireId($user_id);
        } elseif ($type_id === 4) {
            // Pas encore de solution
        } elseif ($type_id === 5) {
            $ecash->setPointVenteId($user_id);
        } elseif ($type_id === 6) {
            $ecash->setEntrepriseId($user_id);
        } elseif ($type_id === 7) {
            $ecash->setPlateformeId($user_id);
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