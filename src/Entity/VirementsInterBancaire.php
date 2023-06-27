<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VirementsInterBancaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * VirementsInterBancaire.
 * @ORM\Table(name="virements_inter_bancaire", indexes={@ORM\Index(name="IDX_329AE03879F37AE5", columns={"user_id"})})
 * @ORM\Entity(repositoryClass=VirementsInterBancaireRepository::class)
 */
#[ApiResource()]
class VirementsInterBancaire
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
     * @var string
     *
     * @ORM\Column(name="numero_bank_envoyeur", type="string", length=255, nullable=false)
     */
    private $numeroBankEnvoyeur;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_bank_receveur", type="string", length=255, nullable=false)
     */
    private $numeroBankReceveur;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float", precision=10, scale=0, nullable=false)
     */
    private $montant;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_transaction", type="datetime", nullable=false)
     */
    private $dateTransaction;

    /**
     * @ORM\Column(name="user_id", type="integer",nullable=false)
     */
    private $idUser;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBankEnvoyeur(): ?string
    {
        return $this->numeroBankEnvoyeur;
    }

    public function setNumeroBankEnvoyeur(string $numeroBankEnvoyeur): self
    {
        $this->numeroBankEnvoyeur = $numeroBankEnvoyeur;

        return $this;
    }

    public function getNumeroBankReceveur(): ?string
    {
        return $this->numeroBankReceveur;
    }

    public function setNumeroBankReceveur(string $numeroBankReceveur): self
    {
        $this->numeroBankReceveur = $numeroBankReceveur;

        return $this;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getDateTransaction(): ?\DateTimeInterface
    {
        return $this->dateTransaction;
    }

    public function setDateTransaction(\DateTimeInterface $dateTransaction): self
    {
        $this->dateTransaction = $dateTransaction;

        return $this;
    }

    public function getIdUser(): ?int
    {
        return $this->idUser;
    }

    public function setIdUser(int $idUser): self
    {
        $this->idUser = $idUser;

        return $this;
    }

}