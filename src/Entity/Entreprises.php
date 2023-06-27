<?php

namespace App\Entity;

class Entreprises
{
    private int $id;
    private string $nom;
    private string $prenoms;
    private string $nomEntreprise;
    private string $urlImage;
    private string $numTelephone;
    private string $pays;

    public function setPays(string $pays): void
    {
        $this->pays = $pays;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function getPays(): string
    {
        return $this->pays;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getNomEntreprise(): string
    {
        return $this->nomEntreprise;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getNumTelephone(): string
    {
        return $this->numTelephone;
    }

    public function getPrenoms(): string
    {
        return $this->prenoms;
    }

    public function getUrlImage(): string
    {
        return $this->urlImage;
    }

    public function setNomEntreprise(string $nomEntreprise): void
    {
        $this->nomEntreprise = $nomEntreprise;
    }

    public function setNumTelephone(string $numTelephone): void
    {
        $this->numTelephone = $numTelephone;
    }

    public function setPrenoms(string $prenoms): void
    {
        $this->prenoms = $prenoms;
    }

    public function setUrlImage(string $urlImage): void
    {
        $this->urlImage = $urlImage;
    }
}
