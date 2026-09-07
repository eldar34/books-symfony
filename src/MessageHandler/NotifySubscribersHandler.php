<?php

namespace App\MessageHandler;

use App\Message\NotifySubscribersMessage;
use App\Repository\BookRepository;
use App\Repository\SubscriberRepository;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
class NotifySubscribersHandler
{
    private const BATCH_LIMIT = 200;

    public function __construct(
        private BookRepository $bookRepository,
        private SubscriberRepository $subscriberRepository,
        private MailerInterface $mailer,
        private MessageBusInterface $bus
    ) {}

    public function __invoke(NotifySubscribersMessage $message): void
    {
        $bookId = $message->getBookId();
        $page = $message->getPage();
        
        $bookData = $this->bookRepository->getFlatDataForNotification($bookId);
        if (!$bookData) return;

        $emails = $this->subscriberRepository->findEmailsByAuthorsPage($bookData['author_ids'], $page, self::BATCH_LIMIT);

        if (empty($emails)) {
            return; // Подписчики закончились, рассылка завершена
        }

        foreach ($emails as $emailAddress) {
            $email = (new Email())
                ->from('noreply@example.com')
                ->to($emailAddress)
                ->subject('Новая книга любимого автора!')
                ->text(sprintf(
                    'Здравствуйте! У автора %s вышла новая книга: "%s".', 
                    $bookData['author_name'], 
                    $bookData['title']
                ));

            $this->mailer->send($email);
        }

        if (count($emails) === self::BATCH_LIMIT) {
            $this->bus->dispatch(new NotifySubscribersMessage($bookId, $page + 1));
        }
    }
}
