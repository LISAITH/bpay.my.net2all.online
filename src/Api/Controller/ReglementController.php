<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\ReglementBPayLink;
use App\Service\APIService\ApiUser;
use App\Service\CommonService;
use App\Service\QrCodeService;
use App\Service\SecurityService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;

class ReglementController extends BaseController
{
    private $entityManager;
    private $commonService;
    private $securityService;
    private $qrCodeService;

    private $apiUser;

    public function __construct(EntityManagerInterface $entityManager, ApiUser $apiUser, CommonService $commonService, QrCodeService $qrCodeService, SecurityService $securityService)
    {
        $this->entityManager = $entityManager;
        $this->apiUser = $apiUser;
        $this->qrCodeService = $qrCodeService;
        $this->commonService = $commonService;
        $this->securityService = $securityService;
    }

    public function __invoke(Request $request, ReglementBPayLink $data)
    {
        $userId = $request->attributes->get('user_id');
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'Unknow user.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $code = $this->commonService->generateBPayLinkReference();
        $dateCreated = new \DateTime('now');
        $expiredDate = $dateCreated->add(new \DateInterval('PT'. 1 .'M'));

        $data->setIsUsed(false);
        $data->setCode($code);
        $dataUriQrCode = $this->qrCodeService->generateQrCodeDataUri($code);
        $data->setExpiredAt($expiredDate);
        $data->setCreateAt($dateCreated);
        $data->setQrCodeImgUrl($dataUriQrCode);
        $uniqueId = Uuid::v4()->toRfc4122();
        $paymentLink = $this->generateUrl('reglement_regler_paiement', ['uniqueId' => $uniqueId], false);
        $data->setLinkPaymentUrl($paymentLink);
        $data->setUniqueId($uniqueId);
        $data->setCurrency(constants::XOF_CURRENCY);
        $data->setUserSender($userId);
        $data->setStatus(constants::PENDING);
        $this->entityManager->persist($data);
        $this->entityManager->flush();

        return $data;
    }
}
