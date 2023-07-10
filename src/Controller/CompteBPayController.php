<?php

namespace App\Controller;

use App\Entity\CompteBPay;
use App\Repository\CompteBPayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CompteBPayController extends AbstractController
{
    
    private $entityManager;
    private CompteBPayRepository $compteBPayRepository;

    public function __construct(CompteBPayRepository $compteBPayRepository, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->compteBPayRepository = $compteBPayRepository;
    }

    #[Route('/get/all/compte/Bpay/{user_id}/{type_id}', name: 'app_get_all_compte_b_pay')]
    public function getAllCompte(int $user_id, int $type_id)
    {
        if ($type_id === 1) {
            $compte = $this->compteBPayRepository->findBy(["particulier_id" => $user_id]);
        } elseif ($type_id === 2) {
            $compte = $this->compteBPayRepository->findBy(["distributeur_id" => $user_id]);
        } elseif ($type_id === 3) {            
            $compte = $this->compteBPayRepository->findBy(["partenaire_id" => $user_id]);
        } elseif ($type_id === 4) {
            // Pas encore de solution
        } elseif ($type_id === 5) {
            $compte = $this->compteBPayRepository->findBy(["pointVente_id" => $user_id]);
        } elseif ($type_id === 6) {
            $compte = $this->compteBPayRepository->findBy(["entreprise_id" => $user_id]);
        } elseif ($type_id === 7) {
            $compte = $this->compteBPayRepository->findBy(["plateforme_id" => $user_id]);
        }

        return $this->json($compte);
    }

    #[Route('/get/one/compte/Bpay/{user_id}/{type_id}', name: 'app_get_one_compte_b_pay')]
    public function getOneCompte(int $user_id, int $type_id)
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
        } elseif ($type_id === 7) {
            $compte = $this->compteBPayRepository->findOneBy(["plateforme_id" => $user_id]);
        }

        return $this->json($compte);
    }

    #[Route('/create/compte/Bpay/{user_id}/{type_id}', name: 'app_new_compte_b_pay')]
    public function new(int $user_id, int $type_id)
    {
        $accountNumber = $this->getRandomText(10);
        $ecash = new CompteBPay();
        $ecash->setNumeroCompte($accountNumber);
        $ecash->setSolde(0);

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

        $this->entityManager->persist($ecash);
        $this->entityManager->flush();

       	return $this->json(['message' => 'Compte B-PAY créé avec succès.'], Response::HTTP_CREATED);
    }

    protected function getRandomText($n): string
    {
        $characters = '0123456789ABCDEFGHILKMNOPQRSTUVWXYZ';
        $randomString = '';

        for ($i = 0; $i < $n; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $randomString .= $characters[$index];
        }

        return $randomString;
    }

    // public function new(Request $request)
    // {
    //     $user_id = $request->get('user_id');
    //     $type_id = $request->get('type_id');

    //     $accountNumber = $this->getRandomText(10);
    //     $ecash = new CompteBPay();
    //     $ecash->setNumeroCompte($accountNumber);
    //     $ecash->setSolde(0);

    //     if ($type_id === 1) {
    //         $ecash->setParticulierId($user_id);
    //     } elseif ($type_id === 2) {
    //         $ecash->setDistributeurId($user_id);
    //     } elseif ($type_id === 3) {
    //         $ecash->setPartenaireId($user_id);
    //     } elseif ($type_id === 4) {
    //         // Pas encore de solution
    //     } elseif ($type_id === 5) {
    //         $ecash->setPointVenteId($user_id);
    //     } elseif ($type_id === 6) {
    //         $ecash->setEntrepriseId($user_id);
    //     }

    //     $this->entityManager->persist($ecash);
    //     $this->entityManager->flush();

    //    	return $this->json(['message' => 'Compte B-PAY créé avec succès.'], Response::HTTP_CREATED);
    // }
}