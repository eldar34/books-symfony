<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
    {

        // Если пользователь уже авторизован, перенаправляем на главную
        if ($this->getUser()) {
            return $this->redirectToRoute('app_book_index');
        }

        // Получаем ошибку авторизации, если она была
        $error = $authenticationUtils->getLastAuthenticationError();
        // Последний введенный пользователем логин
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout', methods: ['GET'])]
    public function logout(): void
    {
        // Этот метод может оставаться пустым, Symfony перехватит его автоматически
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
