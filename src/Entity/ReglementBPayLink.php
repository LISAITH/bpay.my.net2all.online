<?php

namespace App\Entity;

use ApiPlatform\Action\NotFoundAction;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Api\Controller\GetReglementByCodeController;
use App\Api\Controller\ReglementController;
use App\Api\Controller\ValiderReglementController;
use App\Repository\ReglementBPayLinkRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ReglementBPayLinkRepository::class)
 */
#[ApiResource(
    collectionOperations: [
        'post' => [
            'method' => 'POST',
            'controller' => ReglementController::class,
            'read' => false,
            'path' => '/demander-reglement/{user_id}/user',
            'denormalization_context' => ['groups' => ['post:reglement']],
            'openapi_context' => [
                'summary' => 'Permet à utilisateur de demander un règlement en générant un lien de paiement et un code.',
                'description' => 'Permet à utilisateur de demander un règlement en générant un lien de paiement et un code.',
            ],
        ],
        'reglement_by_code' => [
            'method' => 'GET',
            'path' => '/reglement_b_pay_links/{code}',
            'controller' => GetReglementByCodeController::class,
            'read' => false,
            'openapi_context' => [
                    'summary' => 'Récupère un règlement à partir d\'un code',
                    'description' => 'Récupère de rechercher un règlement à partir d\'un code',
                ],
        ],
        'valider_reglement' => [
            'method' => 'POST',
            'path' => '/reglement_b_pay_links/valider/{user_id}/user',
            'controller' => ValiderReglementController::class,
            'denormalization_context' => ['groups' => ['post:validate']],
            'openapi_context' => [
                'summary' => 'Cette opération permet de valider un règlement',
                'description' => 'Cette opération permet de valider un règlement',
            ],
        ],
    ],
    itemOperations: ['get' => ['method' => 'GET', 'controller' => NotFoundAction::class]],
    denormalizationContext: ['groups' => ['post:reglement']],
    normalizationContext: ['groups' => ['post:read']],
    paginationEnabled: false,
)]
class ReglementBPayLink
{
    /**
     * @ORM\Id
     *
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(name="code", type="string",  nullable=false)
     */
    #[Groups(['post:read', 'post:validate'])]
    private $code;
    /**
     * @ORM\Column(name="uniqueId", type="string",  nullable=false)
     */
    #[Groups(['post:read'])]
    private $uniqueId;
    /**
     * @ORM\Column(name="link_payment_url", type="string",  nullable=false)
     */
    #[Groups(['post:read'])]
    private $linkPaymentUrl;
    /**
     * @ORM\Column(name="qr_code_img_url", type="string",  nullable=false)
     */
    #[Groups(['post:read'])]
    private $qrCodeImgUrl;
    /**
     * @ORM\Column(name="is_used", type="boolean",  nullable=false)
     */
    #[Groups(['post:read'])]
    private $isUsed;
    /**
     * @ORM\Column(name="montant", type="float", precision= 10, scale = 2, nullable=false)
     */
    #[Groups(['post:reglement', 'post:read'])]
    #[Assert\NotBlank]
    #[Assert\Positive(message: 'Le montant du règlement doit etre supérieur à zéro')]
    private $montant;
    /**
     * @ORM\Column(name="currency", type="string", length=255, nullable=false)
     */
    #[Groups(['post:read'])]
    private $currency;
    /**
     * @ORM\Column(name="payment_motif", type="string", length=255, nullable=true)
     */
    #[Groups(['post:reglement', 'post:read'])]
    #[Assert\NotBlank(message: 'Le motif est obligatoire')]
    private $paymentMotif;
    /**
     * @ORM\Column(name="user_sender_id", type="integer", nullable=true)
     */
    #[Groups(['post:read'])]
    private $userSender;

    /**
     * @ORM\Column(name="user_receive_id", type="integer", nullable=true)
     */
    #[Groups(['post:read'])]
    private $userReceive;

    /**
     * @ORM\Column(name="create_at", type="datetime", nullable=true)
     */
    #[Groups(['post:read'])]
    private $createAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="expiredAt", type="datetime", nullable=true)
     */
    #[Groups(['post:read'])]
    private $expiredAt;
    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updatedAt", type="datetime", nullable=true)
     */
    #[Groups(['post:read'])]
    private $updatedAt;

    /**
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

    public function getExpiredAt(): \DateTime
    {
        return $this->expiredAt;
    }

    public function __construct()
    {
        $this->createAt = new \DateTime('now');
        $this->expiredAt = new \DateTime('now');
    }


    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updateddAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return mixed
     */
    public function getUniqueId()
    {
        return $this->uniqueId;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    public function setExpiredAt(\DateTime $expiredAt): void
    {
        $this->expiredAt = $expiredAt;
    }

    /**
     * @param mixed $uniqueId
     */
    public function setUniqueId($uniqueId): void
    {
        $this->uniqueId = $uniqueId;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return mixed
     */
    public function getIsUsed()
    {
        return $this->isUsed;
    }

    public function setCreateAt(\DateTime $createAt): void
    {
        $this->createAt = $createAt;
    }

    public function getCreateAt(): \DateTime
    {
        return $this->createAt;
    }

    /**
     * @return mixed
     */
    public function getPaymentMotif()
    {
        return $this->paymentMotif;
    }

    /**
     * @return mixed
     */
    public function getQrCodeImgUrl()
    {
        return $this->qrCodeImgUrl;
    }

    /**
     * @param mixed $code
     */
    public function setCode($code): void
    {
        $this->code = $code;
    }

    /**
     * @param mixed $isUsed
     */
    public function setIsUsed($isUsed): void
    {
        $this->isUsed = $isUsed;
    }

    /**
     * @param mixed $paymentMotif
     */
    public function setPaymentMotif($paymentMotif): void
    {
        $this->paymentMotif = $paymentMotif;
    }

    /**
     * @param mixed $qrCodeImgUrl
     */
    public function setQrCodeImgUrl($qrCodeImgUrl): void
    {
        $this->qrCodeImgUrl = $qrCodeImgUrl;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLinkPaymentUrl(): ?string
    {
        return $this->linkPaymentUrl;
    }

    public function setLinkPaymentUrl(string $linkPaymentUrl): self
    {
        $this->linkPaymentUrl = $linkPaymentUrl;

        return $this;
    }

    public function isIsUsed(): ?bool
    {
        return $this->isUsed;
    }

    public function getMontant(): ?float
    {
        return $this->montant;
    }

    public function setMontant(float $montant): self
    {
        $this->montant = $montant;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserReceive()
    {
        return $this->userReceive;
    }

    /**
     * @return mixed
     */
    public function getUserSender()
    {
        return $this->userSender;
    }

    /**
     * @param mixed $userReceive
     */
    public function setUserReceive($userReceive): void
    {
        $this->userReceive = $userReceive;
    }

    /**
     * @param mixed $userSender
     */
    public function setUserSender($userSender): void
    {
        $this->userSender = $userSender;
    }
}
