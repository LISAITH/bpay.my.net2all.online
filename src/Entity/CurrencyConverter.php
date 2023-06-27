<?php

namespace App\Entity;

use App\Repository\CurrencyConverterRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CurrencyConverterRepository::class)
 */
class CurrencyConverter
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;
    /**
     * @ORM\Column(name="from_currency",type="string",length= 255,nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\Currencies")
     */
    private $fromCurrency;
    /**
     * @ORM\Column(name="to_currency",type="string",length= 255,nullable=false)
     * @ORM\ManyToOne(targetEntity="App\Entity\Currencies")
     */
    private $toCurrency;
    /**
     * @ORM\Column(name="unit_converter",type="float",precision=8,scale=2,nullable=false)
     */
    private $fromCurUnitConverter;


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFromCurrency(): ?string
    {
        return $this->fromCurrency;
    }

    public function setFromCurrency(string $fromCurrency): self
    {
        $this->fromCurrency = $fromCurrency;

        return $this;
    }

    public function getToCurrency(): ?string
    {
        return $this->toCurrency;
    }

    public function setToCurrency(string $toCurrency): self
    {
        $this->toCurrency = $toCurrency;

        return $this;
    }

    public function getFromCurUnitConverter(): ?float
    {
        return $this->fromCurUnitConverter;
    }

    public function setFromCurUnitConverter(float $fromCurUnitConverter): self
    {
        $this->fromCurUnitConverter = $fromCurUnitConverter;

        return $this;
    }
}
