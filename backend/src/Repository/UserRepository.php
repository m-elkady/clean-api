<?php

namespace App\Repository;

use App\Entity\User;
use App\Service\Constants;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
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

    public function findAll(?array $paginateOptions = [], ?array $queryOptions = []): Paginator
    {
        // Paginate Options
        $page = ((int)$paginateOptions['page'] - 1) > 0 ? ((int)$paginateOptions['page'] - 1) : 0;
        $sortBy = $paginateOptions['sortBy'] ?? 'id';
        $order = $paginateOptions['order'] ?? 'ASC';
        $perPage = empty($paginateOptions['perPage']) ? Constants::PAGE_LIMIT : $paginateOptions['perPage'];
        $start = $page * $perPage;

        // Query Options
        $queryBuilder = $this->createQueryBuilder('u');
        foreach ($queryOptions as $key => $value) {
            $queryBuilder->andWhere("u.{$key} LIKE '%{$value}%'");
        }

        $queryBuilder
            ->orderBy('u.' . $sortBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($perPage);

        $paginator = new Paginator($queryBuilder);

        return $paginator;
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
