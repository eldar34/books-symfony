<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Author>
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }

    /**
     * Получает топ авторов по количеству выпущенных книг за конкретный год.
     */
    public function findTopAuthorsByYear(int $year, int $limit = 10): array
    {
        return $this->createQueryBuilder('a')
            ->select('a.id', 'a.fullName', 'COUNT(b.id) as bookCount')
            ->join('a.books', 'b')
            ->where('b.releaseYear = :year')
            ->groupBy('a.id', 'a.fullName')
            ->orderBy('bookCount', 'DESC')
            ->setMaxResults($limit)
            ->setParameter('year', $year)
            ->getQuery()
            ->getResult();
    }

    public function findPaginated(int $page, int $limit): Paginator
    {
        $offset = ($page - 1) * $limit;

        $query = $this->createQueryBuilder('a')
            ->orderBy('a.id', 'ASC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query);
    }

    public function save(Author $author, bool $flush = true): void
    {
        $this->getEntityManager()->persist($author);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Author $author, bool $flush = true): void
    {
        $this->getEntityManager()->remove($author);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
