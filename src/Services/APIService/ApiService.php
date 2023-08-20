<?php

namespace App\Services\APIService;

use App\Entity\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use App\Services\AppServices;

class ApiService
{
    private $httpClient;
    private $parameterBag;
    private AppServices $appServices;

    public function __construct(AppServices $appServices,HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
        $this->appServices = $appServices;
    }

    public function getServiceById(int $serviceId): ?Service
    {
        $newService = null;
        try {
            $baseUri = $this->appServices->getBpayServerAddress().'/services/'.$serviceId;
            $response = $this->httpClient->request('GET', $baseUri, [
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ]);
            if (200 == $response->getStatusCode()) {
                $service = json_decode($response->getContent(true));
                $newService = new Service();
                $newService->setId($service->id);
                $newService->setDescription($service->description);
                $newService->setEtat($service->etat);
                $newService->setUrl($service->url);
                $newService->setAppUrl($service->app_url);
                $newService->setRequiredInstallation($service->required_installation);
                $newService->setLibelle($service->libelle);
                $newService->setLogo($service->logo);
                $newService->setIri($service->{'@id'});
            }
        } catch (\Exception $exception) {
            return null;
        }

        return $newService;
    }



    public function getAllServices(): array
    {
        $servicesList = [];
        $baseUri = $this->appServices->getBpayServerAddress().'/services';
        $response = $this->httpClient->request('GET', $baseUri, [
            'headers' => [
                'Content-Type' => 'application/json',
            ],
        ]);
        if (200 == $response->getStatusCode()) {
            $allServices = json_decode($response->getContent())->{'hydra:member'};
            if (is_array($allServices)) {
                foreach ($allServices as $service) {
                    $newService = new Service();
                    $newService->setId($service->id);
                    $newService->setDescription($service->description);
                    $newService->setEtat($service->etat);
                    $newService->setUrl($service->url);
                    $newService->setAppUrl($service->app_url);
                    $newService->setRequiredInstallation($service->required_installation);
                    $newService->setLibelle($service->libelle);
                    $newService->setLogo($service->logo);
                    $newService->setIri($service->{'@id'});

                    $servicesList[] = $newService;
                }
            }
        }

        return $servicesList;
    }
}