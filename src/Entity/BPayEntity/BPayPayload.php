<?php

namespace App\Entity\BPayEntity;

class BPayPayload
{
    private $status;

    private $payload;

    private $reason;
    private $transactionStatus;
    private $requestId;

    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    public function setReason(?string $reason): void
    {
        $this->reason = $reason;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function getPayload(): ?string
    {
        return $this->payload;
    }

    public function getRequestId(): ?string
    {
        return $this->requestId;
    }

    public function getTransactionStatus(): ?string
    {
        return $this->transactionStatus;
    }

    public function setPayload(?string $payload): void
    {
        $this->payload = $payload;
    }

    public function setRequestId(?string $requestId): void
    {
        $this->requestId = $requestId;
    }

    public function setTransactionStatus(?string $transactionStatus): void
    {
        $this->transactionStatus = $transactionStatus;
    }
}
