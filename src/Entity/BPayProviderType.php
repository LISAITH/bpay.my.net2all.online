<?php

namespace App\Entity;

use App\Repository\BPayProviderTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BPayProviderTypeRepository::class)
 * @ORM\Table(name="bpay_provider_type")
 */
class BPayProviderType
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue
     *
     * @ORM\Column(type="integer")
     *

     */
    private $Id;
    /**
     * @ORM\Column(type="string",name="name",length=255,nullable=true)

     */
    private $name;
    /**

     *
     * @ORM\Column(type="guid",name="guid",nullable=true)
     */
    private $guid;
    /**

     *
     * @ORM\Column(type="string",name="description",length=1000, nullable=true)
     */
    private $description;
    /**
     *
     * @ORM\Column(type="boolean",name="is_available", nullable=true)
     */
    private $isAvailable;
    /**
     *
     * @ORM\Column(type="boolean",name="active", nullable=true)
     */
    private $active;
    /**
     *
     * @ORM\Column(type="string",name="brand",length=255, nullable=true)
     */
    private $brand;
    /**
     *
     * @ORM\Column(type="integer",name="displayOrder", nullable=true)
     */
    private $displayOrder;
    /**

     * @ORM\Column(type="datetime",name="creationDate", nullable=true)
     */
    private $creationDate;
    /**

     * @ORM\Column(type="datetime",name="modificationDate", nullable=true)
     */
    private $modificationDate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\BPayProviderItem",mappedBy="providerType")
     */
    private $providers;

    public function __construct()
    {
        $this->providers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->Id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getGuid(): ?string
    {
        return $this->guid;
    }

    public function setGuid(?string $guid): self
    {
        $this->guid = $guid;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function isIsAvailable(): ?bool
    {
        return $this->isAvailable;
    }

    public function setIsAvailable(?bool $isAvailable): self
    {
        $this->isAvailable = $isAvailable;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): self
    {
        $this->brand = $brand;

        return $this;
    }

    public function getDisplayOrder(): ?int
    {
        return $this->displayOrder;
    }

    public function setDisplayOrder(?int $displayOrder): self
    {
        $this->displayOrder = $displayOrder;

        return $this;
    }

    public function getCreationDate(): ?\DateTimeInterface
    {
        return $this->creationDate;
    }

    public function setCreationDate(?\DateTimeInterface $creationDate): self
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    public function getModificationDate(): ?\DateTimeInterface
    {
        return $this->modificationDate;
    }

    public function setModificationDate(?\DateTimeInterface $modificationDate): self
    {
        $this->modificationDate = $modificationDate;

        return $this;
    }

    /**
     * @return Collection<int, BPayProviderItem>
     */
    public function getProviders(): Collection
    {
        return $this->providers;
    }

    public function addProvider(BPayProviderItem $provider): self
    {
        if (!$this->providers->contains($provider)) {
            $this->providers->add($provider);
            $provider->setProviderType($this);
        }

        return $this;
    }

    public function removeProvider(BPayProviderItem $provider): self
    {
        if ($this->providers->removeElement($provider)) {
            // set the owning side to null (unless already changed)
            if ($provider->getProviderType() === $this) {
                $provider->setProviderType(null);
            }
        }

        return $this;
    }
}
