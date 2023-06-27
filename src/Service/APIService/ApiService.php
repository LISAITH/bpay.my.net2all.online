<?php

namespace App\Service\APIService;

use App\Entity\Service;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class ApiService
{
    private $httpClient;
    private $parameterBag;

    public function __construct(HttpClientInterface $httpClient, ParameterBagInterface $parameterBag)
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    public function getServiceById(int $serviceId): ?Service
    {
        $newService = null;
        try {
            $baseUri = $this->parameterBag->get('API_URL').'/services/'.$serviceId;
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
        $baseUri = $this->parameterBag->get('API_URL').'/services';
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
