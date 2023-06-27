<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\ReglementBPayLink;
use App\Service\APIService\ApiUser;
use App\Service\BPayConfigService;
use App\Service\BusinessService;
use App\Service\CommonService;
use App\Service\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ValiderReglementController extends BaseController
{
    private $entityManager;
    private $businessService;
    private $commonService;
    private $configService;
    private $securityService;
    private $apiUser;

    public function __construct(EntityManagerInterface $entityManager, ApiUser $apiUser, SecurityService $securityService, BPayConfigService $configService, CommonService $commonService, BusinessService $businessService)
    {
        $this->businessService = $businessService;
        $this->entityManager = $entityManager;
        $this->apiUser = $apiUser;
        $this->commonService = $commonService;
        $this->configService = $configService;
        $this->securityService = $securityService;
    }

    public function __invoke(Request $request, ReglementBPayLink $data)
    {
        $userId = $request->attributes->get('user_id');
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'Unkown user.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $findReglement = $this->entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'code' => $data->getCode(),
        ]);
        if (empty($findReglement)) {
            return new JsonResponse(['error' => 'Règlement B-PAY introuvable'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ($findReglement->getIsUsed()) {
            return new JsonResponse(['error' => 'Le règlement B-PAY à été déja validé'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ((new \DateTime('now')) > $findReglement->getExpiredAt()) {
            $findReglement->setStatus(constants::FAILED);
            $this->entityManager->persist($findReglement);
            $this->entityManager->flush($findReglement);

            return new JsonResponse(['error' => 'Le règlement B-PAY a expiré'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        if ($userId === $findReglement->getUser()->getId()) {
            return new JsonResponse(['error' => 'Validation impossible. Vous êtes l\'initiateur du règlementé'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $senderECashAccount = $this->businessService->getECashAccountByUser($connectedUser);
        $receiveECashAccount = $this->businessService->getECashAccountByUser($findReglement->getUser());

        $amount = $findReglement->getMontant();
        $frais = 0;
        $this->commonService->calculateOperationPrice(constants::REGLEMENT_BPAY, $amount, $frais);
        $finalAmount = $amount + $frais;

        if ($senderECashAccount->getSolde() < $finalAmount) {
            return new JsonResponse(['error' => 'Votre solde insuffisant pour finaliser cette opération'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $whoSupportFraisReglement = $this->configService->getParameter(constants::BPAY_PARAMETER, constants::BPAY_PARAMETER, constants::WHO_SUPPORT_TAXE)->getDefaultValue() ?? '';

        switch ($whoSupportFraisReglement) {
            case constants::SENDER:
                $this->businessService->debitECashAccount($senderECashAccount, (int) $finalAmount);
                $this->businessService->creditECashAccount($receiveECashAccount, (int) $amount);
                $isSuccess = true;
                break;
            case constants::RECEIVER:
                $this->businessService->debitECashAccount($senderECashAccount, (int) $amount);
                $this->businessService->creditECashAccount($receiveECashAccount, (int) ($amount - ($finalAmount - $amount)));
                $isSuccess = true;
                break;
            default:
                $this->businessService->debitECashAccount($senderECashAccount, (int) $amount);
                $this->businessService->creditECashAccount($receiveECashAccount, (int) $amount);
                $isSuccess = true;
                break;
        }

        $findReglement->setIsUsed(true);
        $findReglement->setStatus(constants::SUCCESSFUL);
        $this->entityManager->persist($findReglement);
        $this->entityManager->flush($findReglement);

        return $findReglement;
    }
}
