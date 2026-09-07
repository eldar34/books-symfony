<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'authors')]
class Author
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "ФИО автора не может быть пустым")]
    private ?string $fullName = null;

    #[ORM\ManyToMany(targetEntity: Book::class, mappedBy: 'authors')]
    private Collection $books;

    #[ORM\OneToMany(targetEntity: Subscriber::class, mappedBy: 'author', cascade: ['remove'])]
    private Collection $subscribers;

    public function __construct()
    {
        $this->books = new ArrayCollection();
        $this->subscribers = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getFullName(): ?string { return $this->fullName; }
    public function setFullName(string $fullName): static { $this->fullName = $fullName; return $this; }
    public function getBooks(): Collection { return $this->books; }
    public function getSubscribers(): Collection { return $this->subscribers; }
}
