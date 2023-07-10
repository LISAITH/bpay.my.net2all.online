<?php

namespace App\TwigExtension;

use App\Services\APIService\ApiService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class AppExtension extends AbstractExtension
{
    private $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('service', [$this, 'getService']),
        ];
    }

    public function getService(int $serviceId)
    {
        $service = $this->apiService->getServiceById($serviceId);

        return $service;
    }
}