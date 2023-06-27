<?php

namespace App\Entity;

use App\Utils\constants;

class TransfertType
{
    private string $name;
    private string $code;
    private string $color;
    private string $sign;

    public function __construct(string $name, string $code, string $color = null, string $sign)
    {
        $this->sign = $sign;
        $this->code = $code;
        $this->color = $color;
        $this->name = $name;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getSign(): string
    {
        return $this->sign;
    }

    /**
     * @param string $sign
     */
    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public static function loadTransfertTypeList(): ?array
    {
        $transList[] = new TransfertType('Cpte principal - Cpte principal', constants::CP_TO_CP, 'bg-info',
            'fas fa-exchange');
        $transList[] = new TransfertType('Cpte principal - Sous cpte', constants::CP_TO_SC, 'bg-success',
            'fas fa-arrow-alt-circle-down');
        $transList[] = new TransfertType('Sous cpte - Cpte principal', constants::SC_TO_CP, 'bg-warning',
            'fas fa-arrow-alt-circle-up');
        $transList[] = new TransfertType('Sous cpte - Sous cpte', constants::SC_TO_SC, 'bg-danger',
            'fas fa-exchange');

        return $transList;
    }
}
