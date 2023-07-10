<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\BPayTransaction;
use App\Entity\DTO\CreditBPAYAccount;
use App\Entity\Recharges;
use App\Services\APIService\ApiUser;
use App\Services\BusinessService;
use App\Services\CommonService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RechargementCompteController extends BaseController
{
    private $entityManager;

    private $businessService;
    private $apiUser;

    public function __construct(EntityManagerInterface $entityManager, ApiUser $apiUser, CommonService $commonService, BusinessService $businessService)
    {
        $this->apiUser = $apiUser;
        $this->entityManager = $entityManager;
        $this->businessService = $businessService;
    }

    public function __invoke(Request $request, ValidatorInterface $validator): Response
    {
        $requestData = json_decode($request->getContent(), true);
        $amount = $requestData['montant'] ?? null;
        $numeroCompte = $requestData['numeroCompte'] ?? null;
        $numeroPointVente = $requestData['numeroPointVente'] ?? null;

        $crediterCompteBPayDTO = new CreditBPAYAccount();
        $crediterCompteBPayDTO->setNumeroCompte($numeroCompte);
        $crediterCompteBPayDTO->setMontant($amount);

        $errors = $validator->validate($crediterCompteBPayDTO);
        if (count($errors) > 0) {
            $errorMessages = null;
            foreach ($errors as $error) {
                $errorMessages .= $error->getMessage();
            }

            return new JsonResponse(['error' => $errorMessages], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $compteACrediter = $this->businessService->getECashAccountByNumber($numeroCompte);
        if (empty($compteACrediter)) {
            return new JsonResponse(['error' => 'Compte B-PAY introuvable'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        /* $pointVente = $this->entityManager->getRepository(PointVente::class)->findOneBy([
             'id' => (int) $numeroPointVente,
         ]);
         if (!empty($numeroPointVente) && empty($pointVente)) {
             return new JsonResponse(['error' => 'Point de vente introuvable'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
         }*/

        $this->entityManager->beginTransaction();

        $this->businessService->creditECashAccount($compteACrediter, (int) $amount);
        $senderUser = !empty($compteACrediter->getParticulierId()) ?
            $this->apiUser->getParticular($compteACrediter->getParticulierId()) :
            $this->apiUser->getEntreprise($compteACrediter->getEntrepriseId());

        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate(new \DateTime('now'));
        $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
        $bPayTransaction->setStatus(constants::SUCCESSFUL);
        $bPayTransaction->setAmount($amount);
        // TODO
        $bPayTransaction->setUserId($senderUser->getId());
        $bPayTransaction->setTypeOpteration(constants::CREDIT);
        $bPayTransaction->setTransactionTypeName(constants::RECHARGEMENT_BPAY);
        $bPayTransaction->setNote(constants::RECHARGE_BPAY_ACCOUNT);

        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $recharge = new Recharges();
        $recharge->setDateRecharge(new \DateTime('now'));
        $recharge->setMontant($amount);
        $recharge->setPointVente(null);
        $recharge->setCompteBPay($compteACrediter);

        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();

        $this->entityManager->commit();

        return new Response(json_encode(
            [
            'numeroCompte' => $numeroCompte,
            'montantDisponible' => $compteACrediter->getSolde(),
            'montant' => $amount,
            ]), Response::HTTP_OK, ['Content-Type', 'application/json']);
    }
}