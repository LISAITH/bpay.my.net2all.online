<?php

namespace App\Entity;

class Particuliers
{
    private int $id;

    private string  $nom;
    private string $prenom;
    private string $numTel;

    private string $pays;

    private string $genre;

    /**
     * @return string
     */
    public function getGenre(): string
    {
        return $this->genre;
    }

    /**
     * @param string $genre
     */
    public function setGenre(string $genre): void
    {
        $this->genre = $genre;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getNom(): string
    {
        return $this->nom;
    }

    public function getNumTel(): string
    {
        return $this->numTel;
    }

    public function getPays(): string
    {
        return $this->pays;
    }

    public function getPrenom(): string
    {
        return $this->prenom;
    }

    public function setNom(string $nom): void
    {
        $this->nom = $nom;
    }

    public function setNumTel(string $numTel): void
    {
        $this->numTel = $numTel;
    }

    public function setPays(string $pays): void
    {
        $this->pays = $pays;
    }

    public function setPrenom(string $prenom): void
    {
        $this->prenom = $prenom;
    }
}
