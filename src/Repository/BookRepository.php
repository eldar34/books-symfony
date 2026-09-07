<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
     * Возвращает объект Paginator с книгами для конкретной страницы.
     */
    public function findPaginated(int $page, int $limit): Paginator
    {
        $offset = ($page - 1) * $limit;

        $query = $this->createQueryBuilder('b')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        return new Paginator($query);
    }

    public function save(Book $book, bool $flush = true): void
    {
        $this->getEntityManager()->persist($book);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Book $book, bool $flush = true): void
    {
        $this->getEntityManager()->remove($book);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getFlatDataForNotification(int $bookId): ?array
    {
        $book = $this->find($bookId);
        if (!$book) {
            return null;
        }

        $authorIds = [];
        foreach ($book->getAuthors() as $author) {
            $authorIds[] = $author->getId();
        }

        $primaryAuthor = $book->getAuthors()->first();

        return [
            'title' => $book->getTitle(),
            'author_name' => $primaryAuthor ? $primaryAuthor->getFullName() : 'Любимый автор',
            'author_ids' => $authorIds
        ];
    }
}
