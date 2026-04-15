<?php

namespace App\Repository;

use App\Entity\RefreshToken;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RefreshToken>
 */
class RefreshTokenRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RefreshToken::class);
    }

    public function findOneBySelector(string $selector): ?RefreshToken
    {
        return $this->findOneBy(['selector' => $selector]);
    }

    public function revokeAllForUser(User $user): void
    {
        $this->createQueryBuilder('rt')
            ->update()
            ->set('rt.isValid', 'false')
            ->set('rt.revokedAt', ':now')
            ->where('rt.user = :user')
            ->andWhere('rt.isValid = :valid')
            ->setParameter('user', $user)
            ->setParameter('valid', true)
            ->setParameter('now', new \DateTimeImmutable())
            ->getQuery()
            ->execute();
    }

    public function removeExpired(): int
    {
        $now = new \DateTime();

        return $this->createQueryBuilder('rt')
            ->delete(RefreshToken::class, 'rt')
            ->where('rt.expiresAt < :now')
            ->orWhere('rt.isValid = :invalid')
            ->setParameter('now', $now)
            ->setParameter('invalid', false)
            ->getQuery()
            ->execute();
    }

    public function save(RefreshToken $token): void
    {
        $this->getEntityManager()->persist($token);
        $this->getEntityManager()->flush();
    }
}
