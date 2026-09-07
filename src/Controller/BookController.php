<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Service\BookService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class BookController extends AbstractController
{
    public function __construct(
        private BookService $bookService
    ) {}

    // Главная страница: пагинированный список всех книг
    #[Route('/', name: 'app_book_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $paginationData = $this->bookService->getPaginatedBooks($page);

        return $this->render('book/index.html.twig', $paginationData);
    }

    // Создание книги
    #[Route('/books/new', name: 'app_book_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $book = new Book();
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImageFile')->getData();
            $success = $this->bookService->createBook($book, $coverFile);

            if ($success) {
                $this->addFlash('success', 'Книга успешно добавлена!');
            } else {
                $this->addFlash('error', 'Не удалось загрузить обложку книги.');
            }

            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // Просмотр детальной информации о книге
    #[Route('/books/{id}', name: 'app_book_show', methods: ['GET'])]
    public function show(Book $book): Response
    {
        return $this->render('book/show.html.twig', [
            'book' => $book,
        ]);
    }

    // Редактирование книги
    #[Route('/books/{id}/edit', name: 'app_book_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Book $book, Request $request): Response
    {
        $form = $this->createForm(BookType::class, $book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $coverFile = $form->get('coverImageFile')->getData();
            $this->bookService->updateBook($book, $coverFile);

            $this->addFlash('success', 'Изменения сохранены!');
            return $this->redirectToRoute('app_book_index');
        }

        return $this->render('book/edit.html.twig', [
            'form' => $form->createView(),
            'book' => $book
        ]);
    }

    // Удаление книги
    #[Route('/books/{id}/delete', name: 'app_book_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Book $book, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$book->getId(), $request->request->get('_token'))) {
            $this->bookService->deleteBook($book);
            $this->addFlash('success', 'Книга удалена.');
        }

        return $this->redirectToRoute('app_book_index');
    }
}
