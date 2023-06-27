<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetUserSubAccountByService;
use App\Api\Controller\UserSubAccountController;
use App\Repository\SousCompteRepository;
use App\Service\APIService\ApiService;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * SousCompte.
 *
 * @ORM\Table(name="sous_compte", uniqueConstraints={@ORM\UniqueConstraint(name="UNIQ_1FE9A0975056AF58", columns={"numero_sous_compte"})}, indexes={@ORM\Index(name="IDX_1FE9A097DF8F311D", columns={"compte_ecash_id"}), @ORM\Index(name="IDX_1FE9A097ED5CA9E6", columns={"service_id"})})
 *
 * @ORM\Entity(repositoryClass=SousCompteRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'get' => [
            'method' => 'get',
        ],
        'user_sous_comptes' => [
            'method' => 'GET',
            'path' => '/sous-comptes/{user_id}/user',
            'controller' => UserSubAccountController::class,
            'read' => false,
            'pagination_enable' => false,
            'filters' => [],
            'normalization_context' => ['groups' => ['read:collection']],
            'openapi_context' => [
                'summary' => 'Permet récupérer la liste des sous compte d\'un utilisateur',
                'parameters' => [
                ],
            ],
        ],
        'user_sous_compte_by_service' => [
            'method' => 'GET',
            'path' => '/sous-compte/{user_id}/service/{service_id}',
            'read' => false,
            'controller' => GetUserSubAccountByService::class,
            'pagination_enable' => false,
            'filters' => [],
            'openapi_context' => [
                'summary' => 'Permet de récupérer le sous-compte d\'un utilisateur pour un service donné: Ex:CashLine, MagmaERP',
                'parameters' => [
                ],
            ],
        ],
    ],
    itemOperations: [
     'get' => [
                'method' => 'get',
            ],
    ], paginationEnabled: false,
)]
class SousCompte
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
    #[Groups(['read:collection'])]
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="solde", type="float", precision=10, scale=0, nullable=false)
     */
    #[Groups(['read:collection'])]
    private $solde;

    /**
     * @var string
     *
     * @ORM\Column(name="numero_sous_compte", type="string", length=255, nullable=false)
     */
    #[Groups(['read:collection'])]
    private $numeroSousCompte;

    /**
     * @ORM\ManyToOne(targetEntity="CompteBPay")
     *
     * @ORM\JoinColumns({
     *
     *   @ORM\JoinColumn(name="compte_ecash_id", referencedColumnName="id")
     * })
     */
    #[Groups(['read:collection'])]
    private $compteBPay;

    /**
     * @ORM\Column(type="integer",name="service_id", nullable=true)
     */
    private $service_id;

    private static $apiService;

    public function __construct(ApiService $apiService)
    {
        $this->apiService = $apiService;
    }

    public static function loadServiceById(int $id)
    {

        return 'CashLine';
    }

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

    public function getNumeroSousCompte(): ?string
    {
        return $this->numeroSousCompte;
    }

    public function setNumeroSousCompte(string $numeroSousCompte): self
    {
        $this->numeroSousCompte = $numeroSousCompte;

        return $this;
    }

    public function getServiceId(): ?int
    {
        return $this->service_id;
    }

    public function setServiceId(?int $service_id): self
    {
        $this->service_id = $service_id;

        return $this;
    }

    public function getCompteBPay(): ?CompteBPay
    {
        return $this->compteBPay;
    }

    public function setCompteBPay(?CompteBPay $compteBPay): self
    {
        $this->compteBPay = $compteBPay;

        return $this;
    }
}
