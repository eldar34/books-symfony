<?php

namespace App\EventSubscriber;

use App\Event\BookCreatedEvent;
use App\Message\NotifySubscribersMessage;
use App\Repository\SubscriberRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;

class BookCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private MessageBusInterface $bus,
        private SubscriberRepository $subscriberRepository,
        private RequestStack $requestStack
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            BookCreatedEvent::class => 'handle',
        ];
    }

    public function handle(BookCreatedEvent $event): void
    {
        $book = $event->getBook();
        $authors = $book->getAuthors();

        // 1. Собираем сущности авторов этой книги
        $authorIds = [];
        foreach ($authors as $author) {
            $authorIds[] = $author->getId();
        }

        if (empty($authorIds)) {
            return;
        }

        // 2. Делаем быстрый запрос через репозиторий для получения первых 5 уникальных Email подписчиков
        // Для этого мы используем метод, который напишем в репозитории на следующем шаге
        $emails = $this->subscriberRepository->findPreviewEmailsByAuthors($authorIds, 5);

        // Получаем объект сессии для работы с Flash-сообщениями
        $session = $this->requestStack->getSession();

        // 3. Если подписчиков нет — выводим warning и прерываем выполнение (задача в очередь не идет)
        if (empty($emails)) {
            $session->getFlashBag()->add('warning', sprintf(
                '[Очередь] На авторов книги "%s" никто не подписан. Задача в очередь не ставилась.',
                $book->getTitle()
            ));
            return;
        }

        // 4. Создаем и отправляем задачу в очередь, передавая только ID книги
        $this->bus->dispatch(new NotifySubscribersMessage($book->getId()));
        
        // В Symfony Messenger ID сообщения можно получить из специального штампа (TransportMessageIdStamp), 
        // но он доступен только после фактической отправки в транспорт (например, AMQP/Redis). 
        // При использовании Doctrine-транспорта до асинхронной обработки мы пишем "Фоновый режим".
        $jobId = 'Фоновый режим';

        // 5. Формируем строку со списком Email для вывода (не более 3 адресов)
        if (count($emails) > 3) {
            $visibleEmails = array_slice($emails, 0, 3);
            $emailList = implode(', ', $visibleEmails) . '...';
        } else {
            $emailList = implode(', ', $emails);
        }

        // 6. Информируем пользователя через Flash-сообщение Symfony (совместимо с базовым шаблоном twig)
        $session->getFlashBag()->add('success', sprintf(
            '[Очередь] Задача на отправку уведомлений добавлена в очередь (Режим: %s). Письма будут отправлены в фоновом режиме на адреса: %s',
            $jobId,
            $emailList
        ));
    }
}
