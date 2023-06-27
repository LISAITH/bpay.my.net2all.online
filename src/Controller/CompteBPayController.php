<?php

namespace App\Controller;

use App\Entity\CompteBPay;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class CompteBPayController extends AbstractController
{
    
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/compte/b/pay', name: 'app_compte_b_pay')]
    public function index(): Response
    {
        return $this->render('compte_b_pay/index.html.twig', [
            'controller_name' => 'CompteBPayController',
        ]);
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