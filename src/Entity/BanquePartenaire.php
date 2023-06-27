<?php

namespace App\Entity;

use App\Repository\BanquePartenairesRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BanquePartenairesRepository::class)
 */
class BanquePartenaire
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
    private ?int $id = null;
    /**
     * @ORM\Column(name="nom_banque", type="string", length=255, nullable=false)
     */
    private string $nomBanque;
    /**
     * @ORM\Column(name="code_banque", type="string", length=255, nullable=false)
     */
    private string $codeBanque;
    /**
     * @ORM\Column(name="email_address", type="string", length=255, nullable=false)
     */
    private string $emailAddress;
    /**
     * @ORM\Column(name="numero_tel", type="string", nullable=false)
     */
    private string $numeroTel;
    /**
     * @ORM\ManyToOne(targetEntity="Pays")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="pays_id", referencedColumnName="id")
     * })
     */
    private Pays $pays;

    /**
     * @ORM\OneToMany(targetEntity="UserBank", mappedBy="banquePartenaire")
     */
    protected $userBanks;

    /**
     * @ORM\Column(name="date_creation", type="datetime",  nullable=false)
     */
    private \DateTime $dateCreation;
    /**
     * @ORM\Column(name="date_modification", type="datetime",nullable=false)
     */
    private \DateTime $dateModification;
    /**
     * @ORM\Column(name="contract_signed", type="boolean",nullable=false)
     */
    private bool $contractSigned;
    /**
     * @ORM\Column(name="date_contract_signed", type="datetime",nullable=false)
     */
    private \DateTime $dateContractSigned;
    /**
     * @ORM\Column(name="join_file", type="string", length=255,  nullable=false)
     */
    private string $joinFile;

    /**
     * @ORM\Column(name="active", type="boolean",   nullable=false)
     */
    private $active;

    /**
     * @ORM\Column(name="domiciliation", type="string", nullable=false)
     */
    private $domiciliation;

    public function __construct()
    {
        $this->userBanks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomBanque(): ?string
    {
        return $this->nomBanque;
    }


    /**
     * @return mixed
     */
    public function getDomiciliation()
    {
        return $this->domiciliation;
    }

    /**
     * @param mixed $domiciliation
     */
    public function setDomiciliation($domiciliation): void
    {
        $this->domiciliation = $domiciliation;
    }

    public function setNomBanque(string $nomBanque): self
    {
        $this->nomBanque = $nomBanque;

        return $this;
    }

    public function getCodeBanque(): ?string
    {
        return $this->codeBanque;
    }

    public function setCodeBanque(string $codeBanque): self
    {
        $this->codeBanque = $codeBanque;

        return $this;
    }

    public function getEmailAddress(): ?string
    {
        return $this->emailAddress;
    }

    public function setEmailAddress(string $emailAddress): self
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    public function getNumeroTel(): ?string
    {
        return $this->numeroTel;
    }

    public function setNumeroTel(string $numeroTel): self
    {
        $this->numeroTel = $numeroTel;

        return $this;
    }

    /**
     * @param \DateTime $dateModification
     */
    public function setDateModification(\DateTime $dateModification): void
    {
        $this->dateModification = $dateModification;
    }

    /**
     * @param \DateTime $dateCreation
     */
    public function setDateCreation(\DateTime $dateCreation): void
    {
        $this->dateCreation = $dateCreation;
    }

    /**
     * @return \DateTime
     */
    public function getDateModification(): \DateTime
    {
        return $this->dateModification;
    }

    /**
     * @return \DateTime
     */
    public function getDateCreation(): \DateTime
    {
        return $this->dateCreation;
    }

    /**
     * @return \DateTime
     */
    public function getDateContractSigned(): \DateTime
    {
        return $this->dateContractSigned;
    }

    /**
     * @param \DateTime $dateContractSigned
     */
    public function setDateContractSigned(\DateTime $dateContractSigned): void
    {
        $this->dateContractSigned = $dateContractSigned;
    }

    /**
     * @return Pays
     */
    public function getPays(): Pays
    {
        return $this->pays;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param Pays $pays
     */
    public function setPays(Pays $pays): void
    {
        $this->pays = $pays;
    }


    /**
     * @param ArrayCollection $userBanks
     */
    public function setUserBanks(ArrayCollection $userBanks): void
    {
        $this->userBanks = $userBanks;
    }



    public function isContractSigned(): ?bool
    {
        return $this->contractSigned;
    }

    public function setContractSigned(bool $contractSigned): self
    {
        $this->contractSigned = $contractSigned;

        return $this;
    }

    public function getJoinFile(): ?string
    {
        return $this->joinFile;
    }

    public function setJoinFile(string $joinFile): self
    {
        $this->joinFile = $joinFile;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    public function getCountry(): ?Pays
    {
        return $this->country;
    }

    public function setCountry(?Pays $country): self
    {
        $this->country = $country;

        return $this;
    }

    /**
     * @return Collection<int, UserBank>
     */
    public function getUserBanks(): Collection
    {
        return $this->userBanks;
    }

    public function addUserBank(UserBank $userBank): self
    {
        if (!$this->userBanks->contains($userBank)) {
            $this->userBanks->add($userBank);
            $userBank->setBanquePartenaire($this);
        }

        return $this;
    }

    public function removeUserBank(UserBank $userBank): self
    {
        if ($this->userBanks->removeElement($userBank)) {
            // set the owning side to null (unless already changed)
            if ($userBank->getBanquePartenaire() === $this) {
                $userBank->setBanquePartenaire(null);
            }
        }

        return $this;
    }
}
