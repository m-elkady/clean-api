<?php

namespace App\Entity;

use App\Repository\ApiTokenRepository;
use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ApiTokenRepository::class)]
#[ORM\Table(name: 'api_tokens')]
#[ORM\HasLifecycleCallbacks]
#[ORM\Index(fields: ['tokenId'])]
#[ORM\Index(fields: ['user'])]
#[ORM\Index(fields: ['refreshTokenId'])]
class ApiToken
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'apiTokens')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private User $user;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true)]
    private string $tokenId;

    #[ORM\Column(type: Types::STRING, length: 255, unique: true, nullable: true)]
    private ?string $refreshTokenId = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $expiresAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private DateTimeInterface $createdAt;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    private ?DateTimeInterface $revokedAt = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    private bool $isValid = true;

    public function __construct(User $user, string $tokenId, int $ttl = 3600)
    {
        $this->user = $user;
        $this->tokenId = $tokenId;
        $this->createdAt = new DateTimeImmutable();
        $this->expiresAt = (new DateTimeImmutable())->modify("+{$ttl} seconds");
    }

    #[ORM\PrePersist]
    public function onPrePersist(): void
    {
        if (!isset($this->createdAt)) {
            $this->createdAt = new DateTimeImmutable();
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    public function setTokenId(string $tokenId): self
    {
        $this->tokenId = $tokenId;
        return $this;
    }

    public function getRefreshTokenId(): ?string
    {
        return $this->refreshTokenId;
    }

    public function setRefreshTokenId(?string $refreshTokenId): self
    {
        $this->refreshTokenId = $refreshTokenId;
        return $this;
    }

    public function getExpiresAt(): DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function setExpiresAt(DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;
        return $this;
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }

    public function getRevokedAt(): ?DateTimeInterface
    {
        return $this->revokedAt;
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

    public function isValid(): bool
    {
        return $this->isValid && !$this->isExpired();
    }
}
