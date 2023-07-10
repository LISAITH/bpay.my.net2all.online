<?php

namespace App\Services\BPay\MobilePay;

use App\Entity\BPayEntity\BPayPayload;
use App\Entity\BPayEntity\JWT;
use App\Entity\BPayEntity\Momo\RequestToPayData;
use App\Entity\BPayMomoTransaction;
use App\Services\BPayConfigService;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\Exception\TransportException;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MtnMomoService
{
    private $entityManager;
    private $httpClient;
    private $configParameterService;

    public function __construct(BPayConfigService $configParameterService, HttpClientInterface $client, EntityManagerInterface $entityManager)
    {
        $this->configParameterService = $configParameterService;
        $this->entityManager = $entityManager;
        $this->httpClient = $client;
    }

    /**
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     */
    public function createAccessToken(): void
    {
        $collectionParameters = $this->configParameterService->loadApiMomoCollectionServiceParameter(true);

        $base64Encode = base64_encode(sprintf('%s:%s', $collectionParameters->getXReferenceId(), $collectionParameters->getApiKey()));

        $baseUri = $collectionParameters->getHost().'/collection/token/';
        $headers['Authorization'] = sprintf('%s %s', constants::AUTHORIZATION_BASIC, $base64Encode);
        $headers['Ocp-Apim-Subscription-Key'] = $collectionParameters->getOciApimSubscriptionKey();
        $response = $this->httpClient->request('POST', $baseUri, [
            'headers' => $headers,
        ]);
        if (200 === $response->getStatusCode() && !empty($response->getContent())) {
            $content = json_decode($response->getContent());
            $jwtToken = new JWT($content->access_token, $content->token_type, $content->expires_in);
            $this->setAccessToken($jwtToken);
        }
    }

    public function setAccessToken(JWT $token): void
    {
        $paramClass = true ? constants::API_MOMO_PARAMETER_SANDBOX : constants::API_MOMO_PARAMETER_LIVE;
        $this->configParameterService->setParameterValue(constants::API_MOMO_PARAM_GROUP,
            $paramClass, constants::ACCESS_TOKEN, $token->getAccessToken());
        $this->configParameterService->setParameterValue(constants::API_MOMO_PARAM_GROUP,
            $paramClass, constants::TOKEN_TYPE, $token->getTokenType());
        $this->configParameterService->setParameterValue(constants::API_MOMO_PARAM_GROUP,
            $paramClass, constants::TOKEN_EXPIRE_IN, $token->getExpiresIn());
    }

    /**
     * @throws TransportExceptionInterface
     * @throws ServerExceptionInterface
     * @throws RedirectionExceptionInterface
     * @throws ClientExceptionInterface
     * @throws \Exception
     */
    public function requestToPay(RequestToPayData $requestToPayData): ?BPayPayload
    {
        $this->createAccessToken();
        $payload = new BPayPayload();

        try {
            if (!empty($requestToPayData) && !empty($requestToPayData->getExternalId())) {
                $collectionApiParameter = $this->configParameterService->loadApiMomoCollectionServiceParameter(true);

                $baseUri = $collectionApiParameter->getHost().'/collection/v1_0/'.constants::REQUEST_TO_PAY_URI;

                $headers['Authorization'] = sprintf('%s %s', constants::AUTHORIZATION_BEARER, $collectionApiParameter->getAccessToken() ?? '');
                $headers['X-Reference-Id'] = $requestToPayData->getExternalId();

                $headers['X-Target-Environment'] = $collectionApiParameter->getXTargetEnvironnement();
                $headers['Content-Type'] = $collectionApiParameter->getContentType() ?? 'application/json';
                $headers['Ocp-Apim-Subscription-Key'] = $collectionApiParameter->getOciApimSubscriptionKey();

                $newMomoPay = $this->createMtnMomoRequestData($requestToPayData);


                $this->entityManager->persist($newMomoPay);
                $this->entityManager->flush();

                $response = $this->httpClient->request('POST', $baseUri, [
                    'headers' => $headers,
                    'body' => json_encode([
                        'amount' => (string) ceil(floatval($requestToPayData->getAmount())),
                        'currency' => $requestToPayData->getCurrency(),
                        'externalId' => $newMomoPay->getXReferenceId(),
                        'payer' => [
                            'partyIdType' => $requestToPayData->getPayer()->getPartyIdType(),
                            'partyId' => $requestToPayData->getPayer()->getPartyId(),
                        ],
                        'payerMessage' => $requestToPayData->getPayeeNote(),
                        'payeeNote' => $requestToPayData->getPayerMessage(), ]),
                ]);

                switch ($response->getStatusCode()) {
                    case constants::STATUS_202:
                        $payload = $this->requestToPayTransactionStatus($newMomoPay->getXReferenceId());
                        break;
                    case constants::STATUS_400:
                    case constants::STATUS_500:
                    default:
                        $payload->setRequestId($newMomoPay->getXReferenceId());
                        $payload->setStatus(constants::FAILED);
                        break;
                }
            }
        } catch (TransportException $e) {
            $payload->setRequestId($newMomoPay->getXReferenceId());
            $payload->setStatus(constants::FAILED);
        }

        return $payload;
    }

    public function createMtnMomoRequestData(RequestToPayData $payInfos): ?BPayMomoTransaction
    {
        try {
            $newEntity = new BPayMomoTransaction();
            $newEntity->setPayerMessage($payInfos->getPayerMessage());
            $newEntity->setPayeeNote($payInfos->getPayeeNote());
            $newEntity->setCurrency($payInfos->getCurrency());
            $newEntity->setPartyIdType($payInfos->getPayer()->getPartyIdType());
            $newEntity->setPartyId($payInfos->getPayer()->getPartyId());
            $newEntity->setTransactionRef(null);
            $newEntity->setHolderfullName($payInfos->getName());
            $newEntity->setGuid($payInfos->getExternalId());
            $newEntity->setXReferenceId($payInfos->getExternalId());

            return $newEntity;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * @throws TransportExceptionInterface
     * @throws \Exception
     */
    public function requestToPayTransactionStatus(?string $xReferenceId): ?BPayPayload
    {
        $collectionApiParameter = $this->configParameterService->loadApiMomoCollectionServiceParameter(true);
        $headers['Authorization'] = sprintf('%s %s', constants::AUTHORIZATION_BEARER, $collectionApiParameter->getAccessToken() ?? '');
        $headers['X-Target-Environment'] = $collectionApiParameter->getXTargetEnvironnement();
        $headers['Ocp-Apim-Subscription-Key'] = $collectionApiParameter->getOciApimSubscriptionKey();

        $payTransactionEntity = $this->entityManager->getRepository(BPayMomoTransaction::class)->findOneBy([
            'xReferenceId' => $xReferenceId,
        ]);

        if (empty($payTransactionEntity)) {
            throw new \Exception('Transaction not found', 400);
        }
        $baseUri = $collectionApiParameter->getHost().'/collection/v1_0/'.constants::REQUEST_TO_PAY_URI.'/'.$payTransactionEntity->getXReferenceId();
        $response = $this->httpClient->request('GET', $baseUri, [
            'headers' => $headers,
        ]);

        return $this->processResponse(constants::REQUEST_TO_PAY_TRANSACTION, $response, $payTransactionEntity);
    }

    protected function processResponse(string $action, ?object $response, BPayMomoTransaction $transaction): ?BPayPayload
    {
        $payload = new BPayPayload();
        $responseData = json_decode($response->getContent());

        $payload->setRequestId($transaction->getXReferenceId());
        if (!empty($action) && !empty($responseData)) {
            switch ($action) {
                case constants::REQUEST_TO_PAY_TRANSACTION:
                    switch ($response->getStatusCode()) {
                        case constants::STATUS_200:
                            $payload->setPayload($transaction->getXReferenceId());
                            $status = $responseData->status;
                            switch ($responseData->status) {
                                case 'FAILED':
                                case 'REJECTED':
                                case 'TIMEOUT':
                                case 'ONGOING':
                                    $payload->setReason($responseData->reason);
                                    $payload->setStatus($status);
                                    $payload->setReason($responseData->reason);
                                    break;
                                case 'SUCCESSFUL':
                                    $transaction->setTransactionRef($responseData->financialTransactionId);
                                    $payload->setStatus($status);
                                    $payload->setTransactionRef($responseData->financialTransactionId);
                                    break;
                                case 'PENDING':
                                    $payload->setStatus($status);
                                    break;
                                default:
                                    break;
                            }
                            break;
                        default:
                            break;
                        case constants::STATUS_400:
                        case constants::STATUS_500:
                            $payload->setReason($responseData->reason);
                            $payload->setStatus('FAILED');
                    }
                    break;
                default:
                    return null;
            }
            $this->entityManager->persist($transaction);
            $this->entityManager->flush();
        }

        return $payload;
    }
}