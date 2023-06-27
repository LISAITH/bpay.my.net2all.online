<?php

namespace App\Api\Controller;

use App\Controller\BaseController;
use App\Entity\ReglementBPayLink;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetReglementByCodeController extends BaseController
{

    public function __invoke(Request $request, EntityManagerInterface $entityManager)
    {
        $findReglement = $entityManager->getRepository(ReglementBPayLink::class)->findOneBy([
            'code' => $request->attributes->get('code'),
        ]);

        if (empty($findReglement)) {
            return new Response(json_encode(['error' => 'Reglement introuvable']), 400, [
                'Content-Type', 'application/json',
            ]);
        }

        return $findReglement;
    }
}
