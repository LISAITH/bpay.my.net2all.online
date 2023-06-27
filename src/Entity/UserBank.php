<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\UserBanksController;
use App\Repository\UserBanksRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\User;

/**
 * @ORM\Entity(repositoryClass=UserBanksRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'get',
        ],
        'liste_compte_bancaires' => [
            'method' => 'GET',
            'path' => '/banks/user/{userId}',
            'controller' => UserBanksController::class,
            'read' => false,
            'filters' => [],
            'pagination_enable' => false,
            'openapi_context' => [
                'summary' => 'Permet de récupérer la liste des comptes bancaires d\'un utilisateur',
                'parameters' => [
                ],
            ],
        ],
    ],
    itemOperations: [
    'get',
], paginationEnabled: false, )]
class UserBank
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     *
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;
    /**
     * @ORM\Column(name="numero_compte", type="string", nullable=false)
     */
    private $numeroCompte;
    /**
     * @ORM\ManyToOne(targetEntity="BanquePartenaire",inversedBy="userBanks")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="banque_partenaire_id", referencedColumnName="id")
     * })
     */
    private $banquePartenaire;

    /**
     * @var string|null
     *
     * @ORM\Column(name="montant", type="integer", nullable=true)
     */
    private $montant;

    /**
     * @var string|null
     *
     * @ORM\Column(name="devise", type="string",  nullable=true)
     */
    private $devise;

    /**
     * @var string|null
     *
     * @ORM\Column(name="rib_file", type="string", length=255, nullable=true)
     */
    private $ribFile;

    /**
     * @ORM\Column(name="user_id", type="string", nullable=false)
     */
    private $user_id;

    /**
     * @var bool
     *
     * @ORM\Column(name="status", type="boolean", nullable=false)
     */
    private $status;

    /**
     * @return string|null
     */
    public function getDevise(): ?string
    {
        return $this->devise;
    }

    /**
     * @param string|null $devise
     */
    public function setDevise(?string $devise): void
    {
        $this->devise = $devise;
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBank(): ?string
    {
        return $this->nomBank;
    }

    /**
     * @param string|null $montant
     */
    public function setMontant(?string $montant): void
    {
        $this->montant = $montant;
    }

    /**
     * @return string|null
     */
    public function getMontant(): ?string
    {
        return $this->montant;
    }

    public function setNomBank(string $nomBank): self
    {
        $this->nomBank = $nomBank;

        return $this;
    }

    public function getCodeBank(): ?int
    {
        return $this->codeBank;
    }

    public function setCodeBank(int $codeBank): self
    {
        $this->codeBank = $codeBank;

        return $this;
    }

    public function getCodeGuichet(): ?int
    {
        return $this->codeGuichet;
    }

    public function setCodeGuichet(int $codeGuichet): self
    {
        $this->codeGuichet = $codeGuichet;

        return $this;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    public function getCleRib(): ?int
    {
        return $this->cleRib;
    }

    public function setCleRib(int $cleRib): self
    {
        $this->cleRib = $cleRib;

        return $this;
    }

    public function getRibFile(): ?string
    {
        return $this->ribFile;
    }

    public function setRibFile(?string $ribFile): self
    {
        $this->ribFile = $ribFile;

        return $this;
    }

    public function isStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }



    public function getBanquePartenaire(): ?BanquePartenaire
    {
        return $this->banquePartenaire;
    }

    public function setBanquePartenaire(?BanquePartenaire $banquePartenaire): self
    {
        $this->banquePartenaire = $banquePartenaire;

        return $this;
    }

    public function getUserId(): ?string
    {
        return $this->user_id;
    }

    public function setUserId(string $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }


}
