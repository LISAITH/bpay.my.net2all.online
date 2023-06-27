<?php

namespace App\Entity\BPayEntity\Momo;

class ApiMomoParameter
{
    private ?string $primaryKey;
    private ?string $secondaryKey;
    private ?string $xReferenceId;
    private ?string $xCallBack;
    private ?string $ociApimSubscriptionKey;
    private ?string $apiId;
    private ?string $apiKey;
    private ?string $accessToken;
    private ?string $tokenType;
    private ?string $tokenExpiredIn;
    private ?string $xTargetEnvironnement;

    private ?string $contentType;

    private ?string $host;

    public function __construct()
    {
    }

    /**
     * @param string|null $tokenType
     */
    public function setTokenType(?string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    /**
     * @param string|null $host
     */
    public function setHost(?string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string|null $accessToken
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return string|null
     */
    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    /**
     * @return string|null
     */
    public function getHost(): ?string
    {
        return $this->host;
    }

    /**
     * @return string|null
     */
    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    /**
     * @return string|null
     */
    public function getApiId(): ?string
    {
        return $this->apiId;
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @return string|null
     */
    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    /**
     * @return string|null
     */
    public function getOciApimSubscriptionKey(): ?string
    {
        return $this->ociApimSubscriptionKey;
    }

    /**
     * @return string|null
     */
    public function getPrimaryKey(): ?string
    {
        return $this->primaryKey;
    }

    /**
     * @return string|null
     */
    public function getSecondaryKey(): ?string
    {
        return $this->secondaryKey;
    }

    /**
     * @return string|null
     */
    public function getTokenExpiredIn(): ?string
    {
        return $this->tokenExpiredIn;
    }

    /**
     * @return string|null
     */
    public function getXCallBack(): ?string
    {
        return $this->xCallBack;
    }

    /**
     * @return string|null
     */
    public function getXReferenceId(): ?string
    {
        return $this->xReferenceId;
    }

    /**
     * @return string|null
     */
    public function getXTargetEnvironnement(): ?string
    {
        return $this->xTargetEnvironnement;
    }

    /**
     * @param string|null $apiId
     */
    public function setApiId(?string $apiId): void
    {
        $this->apiId = $apiId;
    }

    /**
     * @param string|null $apiKey
     */
    public function setApiKey(?string $apiKey): void
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @param string|null $contentType
     */
    public function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
    }

    /**
     * @param string|null $ociApimSubscriptionKey
     */
    public function setOciApimSubscriptionKey(?string $ociApimSubscriptionKey): void
    {
        $this->ociApimSubscriptionKey = $ociApimSubscriptionKey;
    }

    /**
     * @param string|null $primaryKey
     */
    public function setPrimaryKey(?string $primaryKey): void
    {
        $this->primaryKey = $primaryKey;
    }

    /**
     * @param string|null $secondaryKey
     */
    public function setSecondaryKey(?string $secondaryKey): void
    {
        $this->secondaryKey = $secondaryKey;
    }

    /**
     * @param string|null $tokenExpiredIn
     */
    public function setTokenExpiredIn(?string $tokenExpiredIn): void
    {
        $this->tokenExpiredIn = $tokenExpiredIn;
    }

    /**
     * @param string|null $xCallBack
     */
    public function setXCallBack(?string $xCallBack): void
    {
        $this->xCallBack = $xCallBack;
    }

    /**
     * @param string|null $xReferenceId
     */
    public function setXReferenceId(?string $xReferenceId): void
    {
        $this->xReferenceId = $xReferenceId;
    }

    /**
     * @param string|null $xTargetEnvironnement
     */
    public function setXTargetEnvironnement(?string $xTargetEnvironnement): void
    {
        $this->xTargetEnvironnement = $xTargetEnvironnement;
    }
}
