<?php

namespace App\Services\BPay;

use App\Entity\BPayEntity\BPayPayload;
use App\Entity\BPayEntity\Momo\RequestToPayData;
use App\Entity\BPayMomoTransaction;
use App\Services\BPay\MobilePay\MtnMomoService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class BPayService
{
    private $mtnMomoService;
    private $entityManager;

    public function __construct(MtnMomoService $momoService, EntityManagerInterface $mtnMomoService)
    {
        $this->entityManager = $mtnMomoService;
        $this->mtnMomoService = $momoService;
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function bPayRequestPayment(string $bPayProviderCode, object $data): ?BPayPayload
    {
        $transactionPayload = new BPayPayload();
        switch ($bPayProviderCode) {
            case constants::MOMO_PROVIDER_CODE:
                if (!($data instanceof RequestToPayData)) {
                    throw new \Exception('Unknown request transaction data passed');
                }
                $transactionPayload = $this->mtnMomoService->requestToPay($data);
                break;

            case constants::FLOOZ_PROVIDER_CODE:
            case constants::PAYPAL_PROVIDER_CODE:
            default:
                break;
        }

        return $transactionPayload;
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function requestTransactionStatus(string $bPayProviderCode, string $requestId)
    {
        $transactionPayload = new BPayPayload();
        switch ($bPayProviderCode) {
            case constants::MOMO_PROVIDER_CODE:
                $transactionPayload = $this->mtnMomoService->requestToPayTransactionStatus($requestId);
                break;

            case constants::FLOOZ_PROVIDER_CODE:
            case constants::PAYPAL_PROVIDER_CODE:
            default:
                break;
        }

        return $transactionPayload;
    }

    public function addNewBPayTransaction(BPayMomoTransaction $transaction): bool
    {
        try {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
        }

        return false;
    }

    public function updateBPayTransaction(BPayMomoTransaction $transaction): bool
    {
        try {
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();

            return true;
        } catch (\Exception $e) {
        }

        return false;
    }
}