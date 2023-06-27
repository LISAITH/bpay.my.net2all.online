<?php
namespace  App\Api\Factory;

use ApiPlatform\Core\OpenApi\Model\Operation;
use ApiPlatform\Core\OpenApi\Model\Parameter;
use ApiPlatform\Core\OpenApi\Model\PathItem;
use ApiPlatform\Core\OpenApi\OpenApi;
use ApiPlatform\OpenApi\Factory\OpenApiFactoryInterface;

class OpenApiFactory implements OpenApiFactoryInterface
{
    private $decorated;

    public function __construct(OpenApiFactoryInterface $decorated)
    {
        $this->decorated = $decorated;
    }

    public function __invoke(array $context = []): OpenApi
    {
        $openApi = $this->decorated->__invoke($context);

        foreach ($openApi->getPaths()->getPaths() as $path => $pathItem) {
            // remove identifier parameter from operations which include "#withoutIdentifier" in the description
            foreach (PathItem::$methods as $method) {
                $getter = 'get'.ucfirst(strtolower($method));
                $setter = 'with'.ucfirst(strtolower($method));
                /** @var Operation $operation */
                $operation = $pathItem->$getter();
                if ($operation && preg_match('/#withoutIdentifier/', $operation->getDescription())) {
                    /** @var Parameter[] $parameters */
                    $parameters = $operation->getParameters();
                    foreach ($parameters as $i => $parameter) {
                        if (preg_match('/identifier/i', $parameter->getDescription())) {
                            unset($parameters[$i]);
                            break;
                        }
                    }

                    $description = str_replace('#withoutIdentifier', '', $operation->getDescription());
                    $openApi->getPaths()->addPath($path, $pathItem = $pathItem->$setter($operation->withDescription($description)->withParameters(array_values($parameters))));
                }
            }
        }

        return $openApi;
    }
}
