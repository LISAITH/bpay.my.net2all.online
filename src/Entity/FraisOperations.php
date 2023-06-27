<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\FraisOperationsRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * FraisOperations.
 * @ORM\Table(name="frais_operations")
 * @ORM\Entity(repositoryClass=FraisOperationsRepository::class)
 */
class FraisOperations
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="frais_reglement", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraisReglement;

    /**
     * @var float
     *
     * @ORM\Column(name="frais_ecash_vers_sous_compte", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraisEcashVersSousCompte;

    /**
     * @var float
     *
     * @ORM\Column(name="frais_inter_ecash", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraisInterEcash;

    /**
     * @var float
     *
     * @ORM\Column(name="frais_ecash_bank", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraisEcashBank;

    /**
     * @var float
     *
     * @ORM\Column(name="frais_inter_bank", type="float", precision=10, scale=0, nullable=false)
     */
    private $fraisInterBank;

    /**
     * @ORM\Column(name="active", type="boolean", nullable=false)
     */
    private $active;

    /**
     * @ORM\Column(name="date_creation", type="datetime", nullable=true)
     */
    private $dateCreation;
    /**
     * @ORM\Column(name="date_modification", type="datetime", nullable=true)
     */
    private $dateModification;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFraisReglement(): ?float
    {
        return $this->fraisReglement;
    }

    /**
     * @param float $active
     */
    public function setActive(float $active): void
    {
        $this->active = $active;
    }


    /**
     * @return mixed
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * @return mixed
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * @param mixed $dateCreation
     */
    public function setDateCreation($dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @param mixed $dateModification
     */
    public function setDateModification($dateModification): void
    {
        $this->dateModification = $dateModification;
    }


    /**
     * @return float
     */
    public function getActive(): float
    {
        return $this->active;
    }

    public function setFraisReglement(float $fraisReglement): self
    {
        $this->fraisReglement = $fraisReglement;

        return $this;
    }

    public function getFraisEcashVersSousCompte(): ?float
    {
        return $this->fraisEcashVersSousCompte;
    }

    public function setFraisEcashVersSousCompte(float $fraisEcashVersSousCompte): self
    {
        $this->fraisEcashVersSousCompte = $fraisEcashVersSousCompte;

        return $this;
    }

    public function getFraisInterEcash(): ?float
    {
        return $this->fraisInterEcash;
    }

    public function setFraisInterEcash(float $fraisInterEcash): self
    {
        $this->fraisInterEcash = $fraisInterEcash;

        return $this;
    }

    public function getFraisEcashBank(): ?float
    {
        return $this->fraisEcashBank;
    }

    public function setFraisEcashBank(float $fraisEcashBank): self
    {
        $this->fraisEcashBank = $fraisEcashBank;

        return $this;
    }

    public function getFraisInterBank(): ?float
    {
        return $this->fraisInterBank;
    }

    public function setFraisInterBank(float $fraisInterBank): self
    {
        $this->fraisInterBank = $fraisInterBank;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }
}
