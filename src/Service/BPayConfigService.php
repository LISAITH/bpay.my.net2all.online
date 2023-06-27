<?php

namespace App\Service;

use App\Entity\BPayConfiguration;
use App\Entity\BPayEntity\Momo\ApiMomoParameter;
use App\Entity\BPayEntity\Stripe\StripeParameter;
use App\Utils\constants;
use Doctrine\ORM\EntityManagerInterface;

class BPayConfigService
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getParameter(string $group, string $class, string $paramName): ?BPayConfiguration
    {
        return $this->entityManager->getRepository(BPayConfiguration::class)->findOneBy([
            'paramGroup' => $group,
            'paramClass' => $class,
            'paramName' => $paramName,
        ]);
    }



    public function setParameterValue(string $group, string $class, string $paramName, string $newValue): void
    {
        $parameter = $this->getParameter($group, $class, $paramName);
        if (!empty($parameter)) {
            $parameter->setDefaultValue($newValue);
            $this->entityManager->persist($parameter);
            $this->entityManager->flush();
        }
    }

    public function loadApiMomoCollectionServiceParameter($sandbox = false): ApiMomoParameter
    {
        $paramClass = $sandbox ? constants::API_MOMO_PARAMETER_SANDBOX : constants::API_MOMO_PARAMETER_LIVE;
        $parameter = new ApiMomoParameter();
        $parameter->setTokenType($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::TOKEN_TYPE)->getDefaultValue() ?? null);
        $parameter->setAccessToken($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::ACCESS_TOKEN)->getDefaultValue() ?? null);
        $parameter->setApiId($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::API_ID)->getDefaultValue() ?? null);
        $parameter->setPrimaryKey($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::PRIMARY_KEY)->getDefaultValue() ?? null);
        $parameter->setSecondaryKey($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::SECONDARY_KEY)->getDefaultValue() ?? null);
        $parameter->setOciApimSubscriptionKey($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::OCI_APIM_SUBSCRIPTION_KEY)->getDefaultValue() ?? null);
        $parameter->setXReferenceId($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::X_REFERENCE_ID)->getDefaultValue() ?? null);
        $parameter->setXTargetEnvironnement($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::X_TARGET_ENVIRONEMENT)->getDefaultValue() ?? null);
        $parameter->setApiKey($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::API_KEY)->getDefaultValue() ?? null);
        $parameter->setHost($this->getParameter(constants::API_MOMO_PARAM_GROUP, $paramClass, constants::API_MOMO_HOST)->getDefaultValue() ?? null);
        $parameter->setContentType(null);

        return $parameter;
    }

    public function loadStripeServiceParameter($sandbox = false): ?StripeParameter
    {
        $paramClass = $sandbox ? constants::STRIPE_SANDBOX_PARAMETER : constants::STRIPE_LIVE_PARAMETER;
        $parameter = new StripeParameter();
        $parameter->setSecretKey($this->getParameter(constants::STRIPE_INTEGRATION, $paramClass, constants::STRIPE_SECRET_KEY)->getDefaultValue() ?? null);
        $parameter->setPublicKey($this->getParameter(constants::STRIPE_INTEGRATION, $paramClass, constants::STRIPE_PUBLIC_KEY)->getDefaultValue() ?? null);

        return $parameter;
    }
}
