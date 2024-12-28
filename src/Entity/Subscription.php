<?php

namespace App\Entity;

use App\Repository\SubscriptionRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SubscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Subscription
{
    final public const STATUS_ACTIVE = 'active';
    final public const STATUS_CANCELLED = 'cancelled';
    final public const STATUS_EXPIRED = 'expired';
    
    final public const BILLING_CYCLE_MONTHLY = 'monthly';
    final public const BILLING_CYCLE_YEARLY = 'yearly';
    final public const BILLING_CYCLE_QUARTERLY = 'quarterly';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Please select a category')]
    private ?Category $category = null;

    #[ORM\ManyToOne(inversedBy: 'subscriptions')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(message: 'Please select a provider')]

    private ?Provider $provider = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'The name cannot be empty')]
    #[Assert\Length(
        min: 2,
        max: 255,
        minMessage: 'The name must be at least {{ limit }} characters long',
        maxMessage: 'The name cannot be longer than {{ limit }} characters'
    )]
    private ?string $name = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: 'The price cannot be empty')]
    #[Assert\Positive(message: 'The price must be greater than zero')]
    #[Assert\LessThanOrEqual(
        value: 100000,
        message: 'The price cannot be greater than {{ limit }} €'
    )]
    private ?float $price = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Please select a billing cycle')]
    #[Assert\Choice(
        choices: [self::BILLING_CYCLE_MONTHLY, self::BILLING_CYCLE_QUARTERLY, self::BILLING_CYCLE_YEARLY],
        message: 'Please select a valid billing cycle'
    )]
    private ?string $billingCycle = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Assert\NotBlank(message: 'The renewal date cannot be empty')]
    #[Assert\Type("\DateTimeInterface")]
    #[Assert\GreaterThanOrEqual(
        value: 'today',
        message: 'The renewal date must be today or in the future'
    )]
    private ?\DateTimeInterface $renewalDate = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: 'Please select a status')]
    #[Assert\Choice(
        choices: [self::STATUS_ACTIVE, self::STATUS_CANCELLED, self::STATUS_EXPIRED],
        message: 'Please select a valid status'
    )]
    private ?string $status = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Assert\Length(
        max: 1000,
        maxMessage: 'The notes cannot be longer than {{ limit }} characters'
    )]
    private ?string $notes = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $updatedAt = null;

    public function __construct()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->billingCycle = self::BILLING_CYCLE_MONTHLY;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): static
    {
        $this->category = $category;

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    public function setProvider(?Provider $provider): static
    {
        $this->provider = $provider;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        $this->price = $price;
        return $this;
    }

    public function getBillingCycle(): ?string
    {
        return $this->billingCycle;
    }

    public function setBillingCycle(string $billingCycle): static
    {
        $this->billingCycle = $billingCycle;

        return $this;
    }

    public function getRenewalDate(): ?\DateTimeInterface
    {
        return $this->renewalDate;
    }

    public function setRenewalDate(\DateTimeInterface $renewalDate): static
    {
        $this->renewalDate = $renewalDate;

        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): static
    {
        $this->notes = $notes;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(\DateTimeImmutable $updatedAt): static
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->createdAt = new \DateTimeImmutable();
        $this->updatedAt = new \DateTimeImmutable();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isCancelled(): bool
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    public function isExpired(): bool
    {
        return $this->status === self::STATUS_EXPIRED;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2) . ' €';
    }

    public function getRenewalDays(): int
    {
        $now = new \DateTime();
        return $now->diff($this->renewalDate)->days;
    }

    public function isRenewalSoon(): bool
    {
        return $this->getRenewalDays() <= 7;
    }
}