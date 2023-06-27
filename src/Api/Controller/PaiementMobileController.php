<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\BPayEntity\Momo\PayerToPay;
use App\Entity\BPayEntity\Momo\RequestToPayData;
use App\Entity\BPayMomoTransaction;
use App\Entity\BPayTransaction;
use App\Entity\PaiementMobile;
use App\Service\APIService\ApiUser;
use App\Service\BPay\BPayService;
use App\Service\BPay\MobilePay\MtnMomoService;
use App\Service\BPay\StripePay\StripeService;
use App\Service\BPayConfigService;
use App\Service\BusinessService;
use App\Service\SecurityService;
use App\Utils\constants;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class PaiementMobileController extends BaseController
{
    private $entityManager;
    private $payService;
    private $securityService;
    private $apiUser;

    public function __construct(BusinessService $businessService, ApiUser $apiUser, StripeService $service, MtnMomoService $mtnMomoService, BPayConfigService $configService, SecurityService $authService, SecurityService $securityService, EntityManagerInterface $entityManager, BPayService $payService)
    {
        $this->entityManager = $entityManager;
        $this->apiUser = $apiUser;
        $this->payService = $payService;
        $this->securityService = $securityService;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function __invoke(Request $request, PaiementMobile $data)
    {

        $userId = $request->attributes->get('user_id');
        $connectedUser = $this->apiUser->getUserById($userId);
        if (empty($connectedUser)) {
            return new JsonResponse(['error' => 'Unknow user.'], Response::HTTP_BAD_REQUEST, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $uniqueId = Uuid::v4()->toRfc4122();

        $this->entityManager->beginTransaction();
        $amount = $data->getMontant();

        $phoneNumber = $data->getNumero();
        $operateur = $data->getOperateur();

        $motif = $data->getMotif();

        $operatorList = new ArrayCollection([constants::CELTTIS_PROVIDER_CODE, constants::MOMO_PROVIDER_CODE, constants::MOOV_PROVIDER_CODE]);
        if (!$operatorList->contains($operateur)) {
            return new JsonResponse(['error' => 'Moyen de paiement inconnu'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }
        $holderName = $data->getHolderName();
        $requestToPay = new RequestToPayData();
        $sandbox = true;
        $requestToPay->setCurrency($sandbox ? constants::EUR_CURRENCY : constants::XOF_CURRENCY);
        $requestToPay->setAmount($amount ?? 0);
        $requestToPay->setExternalId($uniqueId);
        $requestToPay->setName($holderName);
        $requestToPay->setPayer(new PayerToPay('MSISDN', sprintf('%s%s', '', str_replace(' ', '', (string) $phoneNumber))));
        $requestToPay->setPayeeNote(sprintf('%s', $motif));
        $requestToPay->setPayerMessage(sprintf('%s', $motif));

        $payload = $this->payService->bPayRequestPayment(constants::MOMO_PROVIDER_CODE, $requestToPay);

        $findMomo = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
            'xReferenceId' => $uniqueId,
        ]);
        if (empty($findMomo)) {
            return new JsonResponse(['error' => 'Moyen de paiement inconnu'], Response::HTTP_FORBIDDEN, ['Content-Type' => 'application/json;charset=UTF-8']);
        }

        $nowPlus6Minute = (new \DateTime('now'))->add(new \DateInterval('PT'. 1 .'M'));
        while (constants::PENDING == $payload->getStatus()) {
            $payload = $this->payService->requestTransactionStatus(constants::MOMO_PROVIDER_CODE, $payload->getRequestId());
            if (new \DateTime('now') > $nowPlus6Minute) {
                $payload->setStatus(constants::FAILED);
                break;
            }
        }

        $bPayTransaction = new BPayTransaction();
        $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
        $bPayTransaction->setTransactionDate(new \DateTime('now'));
        $bPayTransaction->setGuid($uniqueId);
        $bPayTransaction->setStatus($payload->getStatus());
        $bPayTransaction->setAmount($amount);
        $bPayTransaction->setEntityId($uniqueId);
        $bPayTransaction->setUserId($userId);
        $bPayTransaction->setTypeOpteration(null);
        $bPayTransaction->setTransactionTypeName(constants::PAIEMENT_MOBILE);
        $bPayTransaction->setProviderCode(constants::MOMO_PROVIDER_CODE);
        $bPayTransaction->setNote(constants::RECHARGE_BPAY_ACCOUNT);
        $this->entityManager->persist($bPayTransaction);
        $this->entityManager->flush();
        $this->entityManager->commit();

        return $payload;
    }
}
