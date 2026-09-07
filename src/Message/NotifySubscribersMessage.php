<?php

namespace App\Message;

/**
 * Класс-сообщение (DTO) для передачи задачи в очередь Symfony Messenger.
 */
class NotifySubscribersMessage
{
    public function __construct(
        private int $bookId,
        private int $page = 1
    ) {}

    public function getBookId(): int { return $this->bookId; }
    public function getPage(): int { return $this->page; }
}
