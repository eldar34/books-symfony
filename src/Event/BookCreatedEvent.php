<?php

namespace App\Event;

use App\Entity\Book;
use Symfony\Contracts\EventDispatcher\Event;

class BookCreatedEvent extends Event
{
    public function __construct(
        private Book $book
    ) {}

    public function getBook(): Book
    {
        return $this->book;
    }
}
