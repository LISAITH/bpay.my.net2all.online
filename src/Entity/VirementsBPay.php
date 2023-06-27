<?php

namespace App\Entity;

use App\Repository\VirementsBPayRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * VirementsBPay.
 * @ORM\Table(name="virements_bpay", indexes={@ORM\Index(name="IDX_FDE5CC5C1654DEDD", columns={"id_compte_receveur_id"}), @ORM\Index(name="IDX_FDE5CC5CE8A69F7D", columns={"id_compte_envoyeur_id"})})
 * @ORM\Entity(repositoryClass=VirementsBPayRepository::class)
 */
class VirementsBPay
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
     * @ORM\ManyToOne(targetEntity="CompteBPay")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_compte_receveur_id", referencedColumnName="id")
     * })
     */
    private $idCompteReceveur;

    /**
     * @ORM\ManyToOne(targetEntity="CompteBPay")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_compte_envoyeur_id", referencedColumnName="id")
     * })
     */
    private $idCompteEnvoyeur;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getIdCompteReceveur(): ?CompteBPay
    {
        return $this->idCompteReceveur;
    }

    public function setIdCompteReceveur(?CompteBPay $idCompteReceveur): self
    {
        $this->idCompteReceveur = $idCompteReceveur;

        return $this;
    }

    public function getIdCompteEnvoyeur(): ?CompteBPay
    {
        return $this->idCompteEnvoyeur;
    }

    public function setIdCompteEnvoyeur(?CompteBPay $idCompteEnvoyeur): self
    {
        $this->idCompteEnvoyeur = $idCompteEnvoyeur;

        return $this;
    }
}
