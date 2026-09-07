<?php

namespace App\Service;

use App\Entity\Author;
use App\Repository\AuthorRepository;

class AuthorService
{
    private const LIMIT_PER_PAGE = 12;
    
    public function __construct(
        private AuthorRepository $authorRepository
    ) {}

    /**
     * @return Author[]
     */
    public function getAllAuthors(): array
    {
        return $this->authorRepository->findAll();
    }

    public function getPaginatedAuthors(int $page): array
    {
        if ($page < 1) {
            $page = 1;
        }

        $paginator = $this->authorRepository->findPaginated($page, self::LIMIT_PER_PAGE);
        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / self::LIMIT_PER_PAGE);

        return [
            'authors' => $paginator,
            'current_page' => $page,
            'pages_count' => $pagesCount,
            'total_items' => $totalItems,
        ];
    }

    public function createAuthor(Author $author): void
    {
        $this->authorRepository->save($author);
    }

    public function save(Author $author): void
    {
        $this->authorRepository->save($author);
    }

    public function deleteAuthor(Author $author): void
    {
        $this->authorRepository->remove($author);
    }
}
