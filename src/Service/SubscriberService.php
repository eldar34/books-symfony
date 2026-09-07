<?php

namespace App\Service;

use App\Entity\Author;
use App\Entity\Subscriber;
use App\Repository\SubscriberRepository;

class SubscriberService
{
    public function __construct(
        private SubscriberRepository $subscriberRepository
    ) {}

    /**
     * Создает подписку на автора
     */
    public function subscribeToAuthor(Subscriber $subscriber, Author $author): bool
    {
        // Проверяем, нет ли уже такого подписчика у этого автора
        $existingSubscriber = $this->subscriberRepository->findOneByEmailAndAuthor(
            $subscriber->getEmail(),
            $author
        );

        if ($existingSubscriber !== null) {
            return false; // Подписка уже существует
        }

        $subscriber->setAuthor($author);
        $this->subscriberRepository->save($subscriber);

        return true;
    }
}
