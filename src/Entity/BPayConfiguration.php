<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\BPayConfigurationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="bpay_configuration")
 *
 * @ORM\Entity(repositoryClass=BPayConfigurationRepository::class)
 */
class BPayConfiguration
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="param_group",type="string",length= 255,nullable=true)
     */
    private $paramGroup;
    /**
     * @ORM\Column(name="param_class",type="string",length= 255,nullable=true)
     */
    private $paramClass;
    /**
     * @ORM\Column(name="param_name",type="string",length= 255,nullable=true)
     */
    private $paramName;
    /**
     * @ORM\Column(name="default_value",type="string",length= 8000,nullable=true)
     */
    private $defaultValue;
    /**
     *  @ORM\Column(name="create_at",type="datetime", nullable=true)
     */
    private $createAt;
    /**
     *  @ORM\Column(name="update_at",type="datetime", nullable=true)
     */
    private $updateAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    public function getCreateAt(): \DateTime
    {
        return $this->createAt;
    }

    public function getParamClass(): string
    {
        return $this->paramClass;
    }

    public function getParamGroup(): string
    {
        return $this->paramGroup;
    }

    public function getParamName(): string
    {
        return $this->paramName;
    }

    public function getUpdateAt(): \DateTime
    {
        return $this->updateAt;
    }

    public function setCreateAt(\DateTime $createAt): void
    {
        $this->createAt = $createAt;
    }

    public function setDefaultValue(string $defaultValue): void
    {
        $this->defaultValue = $defaultValue;
    }

    public function setParamClass(string $paramClass): void
    {
        $this->paramClass = $paramClass;
    }

    public function setParamGroup(string $paramGroup): void
    {
        $this->paramGroup = $paramGroup;
    }

    public function setParamName(string $paramName): void
    {
        $this->paramName = $paramName;
    }

    public function setUpdateAt(\DateTime $updateAt): void
    {
        $this->updateAt = $updateAt;
    }
}
