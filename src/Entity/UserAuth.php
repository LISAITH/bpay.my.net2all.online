<?php

namespace App\Entity;

use App\Repository\UserAuthRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=UserAuthRepository::class)
 */
class UserAuth
{
    /**
     * @ORM\Id
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;
    /**
     * @ORM\Column(name="secretKey", type="string", nullable=true)
     */
    private string $secretKey;
    /**
     * @ORM\Column(name="lastConnectionDate", type="datetime", nullable=true)
     */
    private $lastConnectionDate;
    /**
     * @ORM\Column(name="isFirstConnection", type="boolean", nullable=true)
     */
    private $isFirstConnection;
    /**
     * @ORM\Column(name="user_id", type="integer", nullable=true)
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSecretKey(): ?string
    {
        return $this->secretKey;
    }

    public function setSecretKey(string $secretKey): self
    {
        $this->secretKey = $secretKey;

        return $this;
    }

    public function getLastConnectionDate(): ?\DateTimeInterface
    {
        return $this->lastConnectionDate;
    }

    public function setLastConnectionDate(?\DateTimeInterface $lastConnectionDate): self
    {
        $this->lastConnectionDate = $lastConnectionDate;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getIsFirstConnection()
    {
        return $this->isFirstConnection;
    }

    /**
     * @param mixed $isFirstConnection
     */
    public function setIsFirstConnection($isFirstConnection): void
    {
        $this->isFirstConnection = $isFirstConnection;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isIsFirstConnection(): ?bool
    {
        return $this->isFirstConnection;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
