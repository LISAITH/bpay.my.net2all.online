<?php

namespace App\Entity;

class Service
{
    private int $id;
    private string $iri;
    private string $libelle;
    private string $description;
    private string $logo;
    private int $etat;
    private string $url;
    private string $app_url;
    private bool $required_installation;
    private string $appUrl;

    public function getAppUrl(): string
    {
        return $this->app_url;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getIri(): string
    {
        return $this->iri;
    }

    /**
     * @param string $iri
     */
    public function setIri(string $iri): void
    {
        $this->iri = $iri;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getEtat(): int
    {
        return $this->etat;
    }

    public function getLibelle(): string
    {
        return $this->libelle;
    }

    public function getLogo(): string
    {
        return $this->logo;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isRequiredInstallation(): bool
    {
        return $this->required_installation;
    }

    public function setAppUrl(string $app_url): void
    {
        $this->app_url = $app_url;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setEtat(int $etat): void
    {
        $this->etat = $etat;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setLibelle(string $libelle): void
    {
        $this->libelle = $libelle;
    }

    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    public function setRequiredInstallation(bool $required_installation): void
    {
        $this->required_installation = $required_installation;
    }

    public function setUrl(string $url): void
    {
        $this->url = $url;
    }
}
