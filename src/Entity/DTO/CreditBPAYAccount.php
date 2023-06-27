<?php

namespace App\Entity\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class CreditBPAYAccount
{
    #[Assert\NotBlank(message: 'Le numéro de compte  est obligatoire')]
    private $numeroCompte;

    #[Assert\NotBlank(message: 'Le montant est obligatoire')]
    #[Assert\Positive(message: 'Le montant doit etre un nombre positif')]
    #[Assert\GreaterThan(value: 0, message: 'Le montant doit etre supérieur à zéro')]
    private $montant;

    private $numeroPointVente;

    /**
     * @return mixed
     */
    public function getNumeroPointVente()
    {
        return $this->numeroPointVente;
    }

    /**
     * @param mixed $numeroPointVente
     */
    public function setNumeroPointVente($numeroPointVente): void
    {
        $this->numeroPointVente = $numeroPointVente;
    }

    /**
     * @return mixed
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * @param mixed $montant
     */
    public function setMontant($montant): void
    {
        $this->montant = $montant;
    }

    /**
     * @return mixed
     */
    public function getNumeroCompte()
    {
        return $this->numeroCompte;
    }

    /**
     * @param mixed $numeroCompte
     */
    public function setNumeroCompte($numeroCompte): void
    {
        $this->numeroCompte = $numeroCompte;
    }
}