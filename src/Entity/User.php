<?php

namespace App\Entity;

class User
{
    private int $id;
    private string $email;
    private array $roles;
    private string $numero;
    private bool $status;
    private string $type;
    private string $imageUrl;
    private int $entrepriseId;
    private int $particulierId;

    public function getId(): int
    {
        return $this->id;
    }



    /**
     * @return int
     */
    public function getEntrepriseId(): int
    {
        return $this->entrepriseId;
    }

    /**
     * @return int
     */
    public function getParticulierId(): int
    {
        return $this->particulierId;
    }

    /**
     * @param int $particulierId
     */
    public function setParticulierId(int $particulierId): void
    {
        $this->particulierId = $particulierId;
    }

    /**
     * @param int $entrepriseId
     */
    public function setEntrepriseId(int $entrepriseId): void
    {
        $this->entrepriseId = $entrepriseId;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function getEmail(): string
    {
        return $this->email;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function getNumero(): string
    {
        return $this->numero;
    }

    public function isStatus(): bool
    {
        return $this->status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function setNumero(string $numero): void
    {
        $this->numero = $numero;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    public function setStatus(bool $status): void
    {
        $this->status = $status;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }
}
