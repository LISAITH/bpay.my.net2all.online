<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\VirementsBancairesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * VirementsBancaires.
 *
 * @ORM\Table(name="virements_bancaires", indexes={@ORM\Index(name="IDX_C638484E36D1F295", columns={"id_compte_bpay_id"})})
 *
 * @ORM\Entity(repositoryClass=VirementsBancairesRepository::class)
 */
#[ApiResource()]
class VirementsBancaires
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
     * @ORM\Column(name="numero_bancaire", type="string", length=255, nullable=false)
     */
    private $numeroBancaire;

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
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;
    /**
     * @ORM\Column(name="beneficiare", type="string", nullable=false)
     */
    private $beneficiaire;

    /**
     * @ORM\Column(name="note", type="string", nullable=false)
     */
    private $note;

    /**
     * @ORM\Column(name="rib_file_name", type="string", nullable=false)
     */
    private $ribFileName;

    /**
     * @var \CompteBPay
     *
     * @ORM\ManyToOne(targetEntity="CompteBPay")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="id_compte_bpay_id", referencedColumnName="id")
     * })
     */
    private $idCompteBPay;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroBancaire(): ?string
    {
        return $this->numeroBancaire;
    }

    public function setNumeroBancaire(string $numeroBancaire): self
    {
        $this->numeroBancaire = $numeroBancaire;

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

    public function getIdCompteEcash(): ?CompteBPay
    {
        return $this->idCompteEcash;
    }

    public function setIdCompteEcash(?CompteBPay $idCompteEcash): self
    {
        $this->idCompteEcash = $idCompteEcash;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getBeneficiaire(): ?string
    {
        return $this->beneficiaire;
    }

    public function setBeneficiaire(string $beneficiaire): self
    {
        $this->beneficiaire = $beneficiaire;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): self
    {
        $this->note = $note;

        return $this;
    }

    public function getRibFileName(): ?string
    {
        return $this->ribFileName;
    }

    public function setRibFileName(string $ribFileName): self
    {
        $this->ribFileName = $ribFileName;

        return $this;
    }

    public function getIdCompteBPay(): ?CompteBPay
    {
        return $this->idCompteBPay;
    }

    public function setIdCompteBPay(?CompteBPay $idCompteBPay): self
    {
        $this->idCompteBPay = $idCompteBPay;

        return $this;
    }
}
