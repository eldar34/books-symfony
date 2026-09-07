<?php

namespace App\Service;

use App\Entity\Book;
use App\Event\BookCreatedEvent;
use App\Repository\BookRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class BookService
{
    private const LIMIT_PER_PAGE = 12;

    public function __construct(
        private BookRepository $bookRepository,
        private FileUploader $fileUploader,
        private EventDispatcherInterface $dispatcher
    ) {}

    /**
     * Получает пагинированный список книг
     */
    public function getPaginatedBooks(int $page): array
    {
        if ($page < 1) {
            $page = 1;
        }

        $paginator = $this->bookRepository->findPaginated($page, self::LIMIT_PER_PAGE);
        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / self::LIMIT_PER_PAGE);

        return [
            'books' => $paginator,
            'current_page' => $page,
            'pages_count' => $pagesCount,
            'total_items' => $totalItems,
        ];
    }

    /**
     * Создает новую книгу с загрузкой обложки.
     */
    public function createBook(Book $book, ?UploadedFile $coverFile): bool
    {
        if ($coverFile) {
            $newFilename = $this->fileUploader->upload($coverFile);
            if ($newFilename) {
                $book->setCoverImage($newFilename);
            } else {
                return false;
            }
        }

        $this->bookRepository->save($book);
        $this->dispatcher->dispatch(new BookCreatedEvent($book));
        return true;
    }

    /**
     * Обновляет книгу, заменяя обложку при необходимости.
     */
    public function updateBook(Book $book, ?UploadedFile $coverFile): void
    {
        if ($coverFile) {
            $this->fileUploader->remove($book->getCoverImage());
            $newFilename = $this->fileUploader->upload($coverFile);
            if ($newFilename) {
                $book->setCoverImage($newFilename);
            }
        }

        $this->bookRepository->save($book);
    }

    /**
     * Удаляет книгу и её обложку.
     */
    public function deleteBook(Book $book): void
    {
        $this->fileUploader->remove($book->getCoverImage());
        $this->bookRepository->remove($book);
    }
}
