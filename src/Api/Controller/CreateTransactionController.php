<?php

namespace App\Api\Controller;

use App\Entity\BPayMomoTransaction;
use App\Entity\DTO\Transaction;
use App\Utils\constants;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;

class CreateTransactionController extends AbstractController
{
    public function __construct()
    {

    }

    public function __invoke(Transaction $transaction)
    {
        /*  $bPayTransaction = new BPayMomoTransaction();
          $bPayTransaction->setCurrency(constants::XOF_CURRENCY);
          $bPayTransaction->setTransactionDate(new \DateTime('now'));
          $bPayTransaction->setGuid(Uuid::v4()->toRfc4122());
          $bPayTransaction->setStatus(constants::SUCCESSFUL);
          $bPayTransaction->setAmount($amount);
          $bPayTransaction->setUser($senderUser);
          $bPayTransaction->setTypeOpteration(constants::CREDIT);
          $bPayTransaction->setTransactionTypeName(constants::RECHARGEMENT_BPAY);
          $bPayTransaction->setNote(constants::RECHARGE_BPAY_ACCOUNT);

          $this->entityManager->persist($bPayTransaction);
          $this->entityManager->flush();*/
    }
}
