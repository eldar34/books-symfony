<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Subscriber;
use App\Form\AuthorType;
use App\Form\SubscriberType;
use App\Service\AuthorService;
use App\Service\SubscriberService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/authors')]
class AuthorController extends AbstractController
{
    public function __construct(
        private AuthorService $authorService
    ) {}

    // Список всех авторов
    #[Route('/', name: 'app_author_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $paginationData = $this->authorService->getPaginatedAuthors($page);

        return $this->render('author/index.html.twig', $paginationData);
    }

    #[Route('/{id}', name: 'app_author_show', methods: ['GET', 'POST'])]
    public function show(Author $author, Request $request, SubscriberService $subscriberService): Response
    {
        $subscriber = new Subscriber();
        $form = $this->createForm(SubscriberType::class, $subscriber);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
    
            $isSubscribed = $subscriberService->subscribeToAuthor($subscriber, $author);

            if ($isSubscribed) {
                $this->addFlash('success', 'Вы успешно подписались на уведомления о новых книгах автора!');
            } else {
                $this->addFlash('info', 'Вы уже подписаны на этого автора.');
            }
            
            return $this->redirectToRoute('app_author_show', ['id' => $author->getId()]);
        }

        return $this->render('author/show.html.twig', [
            'author' => $author,
            'subscriber_form' => $form->createView(),
        ]);
    }

    // Добавление нового автора
    #[Route('/new', name: 'app_author_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $author = new Author();
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->authorService->createAuthor($author);

            $this->addFlash('success', 'Автор успешно добавлен!');
            return $this->redirectToRoute('app_author_index');
        }

        return $this->render('author/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    // Редактирование автора
    #[Route('/{id}/edit', name: 'app_author_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function edit(Author $author, Request $request): Response
    {
        $form = $this->createForm(AuthorType::class, $author);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->authorService->save($author); // Используем метод сохранения изменений

            $this->addFlash('success', 'Изменения успешно сохранены!');
            return $this->redirectToRoute('app_author_index');
        }

        return $this->render('author/edit.html.twig', [
            'form' => $form->createView(),
            'author' => $author,
        ]);
    }

    // Безопасное удаление автора
    #[Route('/{id}/delete', name: 'app_author_delete', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function delete(Author $author, Request $request): Response
    {
        if ($this->isCsrfTokenValid('delete'.$author->getId(), $request->request->get('_token'))) {
            $this->authorService->deleteAuthor($author);
            $this->addFlash('success', 'Автор успешно удален.');
        }

        return $this->redirectToRoute('app_author_index');
    }
}
