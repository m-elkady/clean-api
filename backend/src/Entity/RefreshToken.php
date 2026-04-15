<?php

namespace App\Entity;

use App\Repository\RefreshTokenRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RefreshTokenRepository::class)]
#[ORM\Table(name: 'refresh_tokens')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(fields: ['selector'])]
#[ORM\Index(fields: ['user'])]
class RefreshToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'refreshTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::STRING, length: 24, unique: true)]
    private string $selector;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $hashedValidator;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $expiresAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeInterface $revokedAt = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isValid = true;

    private ?string $plainValidator = null;

    public function __construct(User $user, string $selector, string $hashedValidator, DateTimeInterface $expiresAt)
    {
        $this->user = $user;
        $this->selector = $selector;
        $this->hashedValidator = $hashedValidator;
        $this->expiresAt = $expiresAt;
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getSelector(): string
    {
        return $this->selector;
    }

    public function getHashedValidator(): string
    {
        return $this->hashedValidator;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getRevokedAt(): ?DateTimeInterface
    {
        return $this->revokedAt;
    }

    /**
     * Set plain validator (not persisted, only for initial toString() call)
     */
    public function setPlainValidator(string $validator): void
    {
        $this->plainValidator = $validator;
    }

    /**
     * Validate a token string in format "selector.validator"
     */
    public function validate(string $token): bool
    {
        $parts = explode('.', $token);

        if (count($parts) !== 2) {
            return false;
        }

        [$selector, $validator] = $parts;

        if ($selector !== $this->selector) {
            return false;
        }

        return hash('sha256', $validator) === $this->hashedValidator;
    }

    /**
     * Convert to string for returning to client (selector.validator format)
     */
    public function toString(): string
    {
        return $this->selector . '.' . ($this->plainValidator ?? '');
    }

    public function revoke(): void
    {
        $this->isValid = false;
        $this->revokedAt = new DateTimeImmutable();
    }

    public function isRevoked(): bool
    {
        return !$this->isValid || $this->revokedAt !== null;
    }

    public function isExpired(): bool
    {
        return new DateTime() > $this->expiresAt;
    }

    public function isValidToken(): bool
    {
        return $this->isValid && !$this->isExpired();
    }
}
