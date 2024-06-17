<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\UnitOfWork;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 *
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    private UnitOfWork $uow;

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
        $this->uow = $this->getEntityManager()->getUnitOfWork();
    }

    public function getEntityManager(): EntityManagerInterface
    {
        return parent::getEntityManager();
    }

    public function add(User $entity): bool
    {
        $this->getEntityManager()->persist($entity);

        $saved = $this->uow->isEntityScheduled($entity);

        $this->getEntityManager()->flush();

        return $saved;
    }

    public function remove(User $entity): bool
    {
        $this->getEntityManager()->remove($entity);

        $deleted = $this->uow->isEntityScheduled($entity);

        $this->getEntityManager()->flush();

        return $deleted;
    }

    public function findAll(?array $paginateOptions = []): array
    {
        $sortBy = $paginateOptions['sortBy'] ?? 'id';
        $order = $paginateOptions['order'] ?? 'ASC';
        $start = $paginateOptions['start'] ?? 0;
        $perPage = empty($paginateOptions['perPage']) ? 20 : $paginateOptions['perPage'];

        return $this->createQueryBuilder('u')
            ->orderBy('u.' . $sortBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($perPage)
            ->getQuery()
            ->getResult();

    }

//    /**
//     * @return Product[] Returns an array of Product objects
//     */


//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
