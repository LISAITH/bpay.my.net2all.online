<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\RechargesRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Recharges.
 *
 * @ORM\Table(name="recharges", indexes={@ORM\Index(name="IDX_7D4DE46DF8F311D", columns={"compte_bpay_id"}), @ORM\Index(name="IDX_7D4DE46EFA24D68", columns={"point_vente_id"})})
 *
 * @ApiResource()
 *
 * @ORM\Entity(repositoryClass=RechargesRepository::class)
 */
class Recharges
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
     * @ORM\Column(name="date_recharge", type="datetime", nullable=false)
     */
    private $dateRecharge;

    /**
     * @ORM\ManyToOne(targetEntity="CompteBPay")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="compte_bpay_id", referencedColumnName="id")
     * })
     */
    private $compteBPay;

    /**
     * @ORM\Column(type="integer",name="point_vente_id", nullable=true)
     */
    private $pointVente;

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

    public function getDateRecharge(): ?\DateTimeInterface
    {
        return $this->dateRecharge;
    }

    public function setDateRecharge(\DateTimeInterface $dateRecharge): self
    {
        $this->dateRecharge = $dateRecharge;

        return $this;
    }

    public function getPointVente(): ?int
    {
        return $this->pointVente;
    }

    public function setPointVente(?int $pointVente): self
    {
        $this->pointVente = $pointVente;

        return $this;
    }

    public function getCompteBPay(): ?CompteBPay
    {
        return $this->compteBPay;
    }

    public function setCompteBPay(?CompteBPay $compteBPay): self
    {
        $this->compteBPay = $compteBPay;

        return $this;
    }
}