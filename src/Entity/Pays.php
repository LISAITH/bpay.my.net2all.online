<?php

namespace App\Entity;
use ApiPlatform\Metadata\ApiResource;
use App\Repository\PaysRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Pays
 * @ORM\Table(name="pays")
 * @ORM\Entity(repositoryClass=PaysRepository::class)
 */
class Pays
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="libelle_pays", type="string", length=255, nullable=false)
     */
    private $libellePays;

    /**
     * @var string
     *
     * @ORM\Column(name="indicatif", type="string", length=255, nullable=false)
     */
    private $indicatif;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer", nullable=false)
     */
    private $status;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibellePays(): ?string
    {
        return $this->libellePays;
    }

    public function setLibellePays(string $libellePays): self
    {
        $this->libellePays = $libellePays;

        return $this;
    }

    public function getIndicatif(): ?string
    {
        return $this->indicatif;
    }

    public function setIndicatif(string $indicatif): self
    {
        $this->indicatif = $indicatif;

        return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }


}
