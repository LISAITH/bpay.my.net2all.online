<?php

namespace App\Entity\BPayEntity;

class JWT
{
    private $accessToken;
    private $tokenType;
    private $expiresIn;

    private $scope;
    private $refreshToken;
    private $refreshTokenExpiresIn;

    public function __construct(string $accessToken, string $tokenType, int $expiresIn, string $scope = null, string $refreshToken = null)
    {
        $this->refreshToken = $refreshToken;
        $this->accessToken = $accessToken;
        $this->tokenType = $tokenType;
        $this->scope = $scope;
        $this->expiresIn = $expiresIn;
    }

    public function getTokenType(): ?string
    {
        return $this->tokenType;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    public function setTokenType(?string $tokenType): void
    {
        $this->tokenType = $tokenType;
    }

    public function setScope(?string $scope): void
    {
        $this->scope = $scope;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function getExpiresIn(): ?string
    {
        return $this->expiresIn;
    }

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getRefreshTokenExpiresIn(): ?string
    {
        return $this->refreshTokenExpiresIn;
    }

    public function setExpiresIn(?string $expiresIn): void
    {
        $this->expiresIn = $expiresIn;
    }

    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function setRefreshTokenExpiresIn(?string $refreshTokenExpiresIn): void
    {
        $this->refreshTokenExpiresIn = $refreshTokenExpiresIn;
    }
}
