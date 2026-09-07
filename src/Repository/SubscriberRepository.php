<?php

namespace App\Repository;

use App\Entity\Subscriber;
use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Subscriber>
 */
class SubscriberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Subscriber::class);
    }

    public function save(Subscriber $subscriber, bool $flush = true): void
    {
        $this->getEntityManager()->persist($subscriber);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Subscriber $subscriber, bool $flush = true): void
    {
        $this->getEntityManager()->remove($subscriber);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findOneByEmailAndAuthor(string $email, Author $author): ?Subscriber
    {
        return $this->createQueryBuilder('s')
            ->where('s.email = :email')
            ->andWhere('s.author = :author')
            ->setParameter('email', $email)   
            ->setParameter('author', $author) 
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPreviewEmailsByAuthors(array $authorIds, int $limit): array
    {
        $qb = $this->createQueryBuilder('s')
            ->select('DISTINCT s.email')
            ->where('s.author IN (:authorIds)')
            ->setParameter('authorIds', $authorIds)
            ->setMaxResults($limit);

        $results = $qb->getQuery()->getScalarResult();

        return array_column($results, 'email');
    }

    public function findEmailsByAuthorsPage(array $authorIds, int $page, int $limit): array
    {
        $results = $this->createQueryBuilder('s')
            ->select('DISTINCT s.email')
            ->where('s.author IN (:authorIds)')
            ->setParameter('authorIds', $authorIds)
            ->orderBy('s.email', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery()
            ->getScalarResult();

        return array_column($results, 'email');
    }
}
