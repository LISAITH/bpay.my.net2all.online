<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BPayPaymentProviderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BPayPaymentProviderItemRepository::class)
 */
#[ApiResource(
    collectionOperations: ['get' => [
        'method' => 'GET',
        'summary' => 'Recupère la liste de tous les moyens de paiement actifs disponible sur B-PAY',
    ]],
    itemOperations: ['get' => ['method' => 'GET']])]
class BPayProviderItem
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    private $id = null;
    /**
     * @ORM\Column(type="string",name="name",length=255,nullable=true)
     */
    private $name;
    /**
     * @ORM\Column(type="guid",name="guid",nullable=true)
     */
    private $guid;
    /**
     * @ORM\Column(type="string",name="description",length=255,nullable=true)
     */
    private $description;
    /**
     * @ORM\Column(type="string",name="displayName",length=255,nullable=true)
     */
    private $displayName;
    /**
     * @ORM\Column(type="boolean",name="active",nullable=true)
     */
    private $active;
    /**
     * @ORM\Column(type="string",name="providerCode",length=50,  nullable=true)
     */
    private $providerCode;
    /**
     * @ORM\Column(type="integer",name="displayOrder", nullable=true)
     */
    private $displayOrder;
    /**
     * @ORM\Column(type="string",name="brand", nullable=true)
     */
    private $brand;

    /**
     * @ORM\JoinColumn(name="provider_type", referencedColumnName="id",onDelete="CASCADE")
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\BPayProviderType",inversedBy="providers")
     */
    private $providerType;

    /**
     * @ORM\Column(type="string",name="dominant_color",nullable=true)
     */
    private $dominantColor;
    /**
     * @ORM\Column(type="string",name="dominant_text",nullable=true)
     */
    private $dominantText;
    /**
     * @ORM\Column(type="boolean",name="is_internal",nullable=true)
     */
    private $isInternal;

    /**
     * @return mixed
     */
    public function getIsInternal()
    {
        return $this->isInternal;
    }

    /**
     * @param mixed $isInternal
     */
    public function setIsInternal($isInternal): void
    {
        $this->isInternal = $isInternal;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): void
    {
        $this->guid = $guid;
    }

    public function setProviderCode(?string $providerCode): void
    {
        $this->providerCode = $providerCode;
    }

    public function getProviderCode(): ?string
    {
        return $this->providerCode;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDisplayName(): ?string
    {
        return $this->displayName;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function setDisplayName(?string $displayName): void
    {
        $this->displayName = $displayName;
    }

    public function setDisplayOrder(?int $displayOrder): void
    {
        $this->displayOrder = $displayOrder;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function getDominantColor(): ?string
    {
        return $this->dominantColor;
    }

    public function setDominantColor(?string $dominantColor): self
    {
        $this->dominantColor = $dominantColor;

        return $this;
    }

    public function getDominantText(): ?string
    {
        return $this->dominantText;
    }

    public function setDominantText(?string $dominantText): self
    {
        $this->dominantText = $dominantText;

        return $this;
    }

    public function getProviderType(): ?BPayProviderType
    {
        return $this->providerType;
    }

    public function setProviderType(?BPayProviderType $providerType): self
    {
        $this->providerType = $providerType;

        return $this;
    }

    public function isIsInternal(): ?bool
    {
        return $this->isInternal;
    }
}
