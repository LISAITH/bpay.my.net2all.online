<?php

namespace App\Entity;

use App\Repository\ReglementsEcashRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * ReglementsBPay.
 *
 
 *
 * @ORM\Table(name="reglements_bpay", indexes={@ORM\Index(name="IDX_4E2E184826F7B8E8", columns={"id_sous_compte_envoyeur_id"}), @ORM\Index(name="IDX_4E2E1848D805F948", columns={"id_sous_compte_receveur_id"})})
 *
 * @ORM\Entity(repositoryClass=ReglementsEcashRepository::class)
 */
class ReglementsBPay
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
     * @var \SousCompte
     *
     * @ORM\ManyToOne(targetEntity="SousCompte")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_sous_compte_envoyeur_id", referencedColumnName="id")
     * })
     */
    private $idSousCompteEnvoyeur;

    /**
     * @var \SousCompte
     *
     * @ORM\ManyToOne(targetEntity="SousCompte")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_sous_compte_receveur_id", referencedColumnName="id")
     * })
     */
    private $idSousCompteReceveur;

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

    public function getIdSousCompteEnvoyeur(): ?SousCompte
    {
        return $this->idSousCompteEnvoyeur;
    }

    public function setIdSousCompteEnvoyeur(?SousCompte $idSousCompteEnvoyeur): self
    {
        $this->idSousCompteEnvoyeur = $idSousCompteEnvoyeur;

        return $this;
    }

    public function getIdSousCompteReceveur(): ?SousCompte
    {
        return $this->idSousCompteReceveur;
    }

    public function setIdSousCompteReceveur(?SousCompte $idSousCompteReceveur): self
    {
        $this->idSousCompteReceveur = $idSousCompteReceveur;

        return $this;
    }
}
