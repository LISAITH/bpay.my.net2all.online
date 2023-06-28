<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\UserBPayAccount;
use App\Repository\CompteBPayRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * CompteBPay.
 *
 * @ORM\Table(name="compte_bpay", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_49ECD2EE9731415A", columns={"numero_compte"}), @ORM\UniqueConstraint(name="UNIQ_49ECD2EEA4AEAFEA", columns={"entreprise_id"}), @ORM\UniqueConstraint(name="UNIQ_49ECD2EEA89E0E67", columns={"particulier_id"})})
 * @ORM\Entity(repositoryClass=CompteBPayRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'getOneCompteBpay' => [
            'method' => 'GET',
            'path' => '/one/compte/bpay/{user_id}/{type_id}',
            'controller' => UserBPayAccount::class,
            'read' => false,
            'paginationEnabled' => false,
            'filters' => [],
            'normalizationContext' => ['groups' => ['read:collection']],
            'openapi_context' => [
                'summary' => 'Récupère le compte B-PAY d\'un utilisateur selon son type',
                'description' => 'Récupère le compte B-PAY d\'un utilisateur selon son type',
                'parameters' => [
                    [
                        'name' => 'user_id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                        ],
                    ],
                    [
                        'name' => 'type_id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ],
        'createCompteBpay' => [
            'method' => 'POST',
            'path' => '/create/compte/Bpay/user/{user_id}/{type_id}',
            'controller' => UserBPayAccount::class,
            'deserialize' => false,
            'read' => false,
            'paginationEnabled' => false,
            'filters' => [],
            'denormalizationContext' => ['groups' => ['write:collection']],
            'openapi_context' => [
                'summary' => 'Crée le compte B-PAY d\'un utilisateur selon son type',
                'description' => 'Crée le compte B-PAY d\'un utilisateur selon son type',
                'parameters' => [
                    [
                        'name' => 'user_id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                        ],
                    ],
                    [
                        'name' => 'type_id',
                        'in' => 'path',
                        'required' => true,
                        'schema' => [
                            'type' => 'integer',
                        ],
                    ],
                ],
            ],
        ],
    ],
    itemOperations: ['put', 'patch', 'get'],
    normalizationContext: ['groups' => ['read:collection']],
    denormalizationContext: ['groups' => ['write:collection']],
    paginationEnabled: false
)]
class CompteBPay
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups(['read:collection'])]
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    #[Groups(['read:collection', 'write:collection'])]
    private $solde;

    /**
     * @ORM\Column(type="string", length=255)
     */
    #[Groups(['read:collection'])]
    private $numeroCompte;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $entreprise_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $particulier_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $partenaire_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $distributeur_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $pointVente_id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    #[Groups(['read:collection'])]
    private $plateforme_id;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSolde(): ?float
    {
        return $this->solde;
    }

    public function setSolde(float $solde): self
    {
        $this->solde = $solde;

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

    public function getEntrepriseId(): ?int
    {
        return $this->entreprise_id;
    }

    public function setEntrepriseId(?int $entreprise_id): self
    {
        $this->entreprise_id = $entreprise_id;

        return $this;
    }

    public function getParticulierId(): ?int
    {
        return $this->particulier_id;
    }

    public function setParticulierId(?int $particulier_id): self
    {
        $this->particulier_id = $particulier_id;

        return $this;
    }

    public function getPartenaireId(): ?int
    {
        return $this->partenaire_id;
    }

    public function setPartenaireId(?int $partenaire_id): self
    {
        $this->partenaire_id = $partenaire_id;

        return $this;
    }

    public function getDistributeurId(): ?int
    {
        return $this->distributeur_id;
    }

    public function setDistributeurId(?int $distributeur_id): self
    {
        $this->distributeur_id = $distributeur_id;

        return $this;
    }

    public function getPointVenteId(): ?int
    {
        return $this->pointVente_id;
    }

    public function setPointVenteId(?int $pointVente_id): self
    {
        $this->pointVente_id = $pointVente_id;

        return $this;
    }

    public function getPlateformeId(): ?int
    {
        return $this->plateforme_id;
    }

    public function setPlateformeId(?int $plateforme_id): self
    {
        $this->plateforme_id = $plateforme_id;

        return $this;
    }
}