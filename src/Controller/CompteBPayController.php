<?php

namespace App\Controller;

use App\Entity\CompteBPay;
use App\Entity\VirementsBPay;
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

    #[Route('/get/one/compte/Bpay/by/number/account/{number_account}', name: 'app_get_one_compte_b_pay_by_number_account')]
    public function getOneCompteByNumberAccount(string $number_account)
    {
        $compte = $this->compteBPayRepository->findOneBy(["numeroCompte" => $number_account]);
        return $this->json($compte);
    }

    #[Route('/create/compte/Bpay/{user_id}/{type_id}', name: 'app_new_compte_b_pay')]
    public function createCompte(int $user_id, int $type_id)
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

    #[Route('/virement/compte/Bpay/{bpay_depot_id}/{bpay_retrait_id}/{montant}', name: 'app_virement_compte_b_pay')]
    public function virementCompte(int $bpay_depot_id, int $bpay_retrait_id, float $montant)
    {
        $accountDepot = $this->compteBPayRepository->findOneBy(["id" => $bpay_depot_id]);
        $accountRetrait = $this->compteBPayRepository->findOneBy(["id" => $bpay_retrait_id]);

        $virement = new VirementsBPay();
        $virement->setMontant($montant);
        $virement->setDateTransaction(new \DateTime("now"));
        $virement->setIdCompteReceveur($accountDepot);
        $virement->setIdCompteEnvoyeur($accountRetrait);
        $this->entityManager->persist($virement);
        
        $accountDepot->setSolde($accountDepot->getSolde()+$montant);
        $this->entityManager->persist($accountDepot);

        $accountRetrait->setSolde($accountRetrait->getSolde()-$montant);
        $this->entityManager->persist($accountRetrait);

        $this->entityManager->flush();

       	return $this->json(['message' => 'Virement effectué avec succès.'], Response::HTTP_CREATED);
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
}