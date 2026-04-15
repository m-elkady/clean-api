<?php

namespace App\Repository;

use App\Entity\ApiToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ApiToken>
 */
class ApiTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ApiToken::class);
    }

    public function findOneByTokenId(string $tokenId): ?ApiToken
    {
        return $this->findOneBy(['tokenId' => $tokenId]);
    }

    public function revokeAllForUser(User $user): void
    {
        $this->getEntityManager()
            ->createQuery('UPDATE App\Entity\ApiToken t SET t.isValid = false, t.revokedAt = CURRENT_TIMESTAMP() WHERE t.user = :user')
            ->setParameter('user', $user)
            ->execute();
    }

    public function deleteExpired(): int
    {
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete(ApiToken::class, 't')
            ->where('t.expiresAt < :now')
            ->setParameter('now', new \DateTimeImmutable());

        return $qb->getQuery()->execute();
    }
}
