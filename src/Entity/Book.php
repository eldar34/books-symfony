<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Table(name: 'books')]
class Book
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Название книги не должно быть пустым")]
    private ?string $title = null;

    #[ORM\Column(type: Types::INTEGER)]
    #[Assert\NotBlank(message: "Укажите год выпуска")]
    #[Assert\Range(min: 1000, max: 2030, notInRangeMessage: "Некорректный год выпуска")]
    private ?int $releaseYear = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 20)]
    #[Assert\NotBlank(message: "ISBN обязателен для заполнения")]
    #[Assert\Length(
        min: 10,
        max: 13,
        minMessage: "ISBN должен содержать минимум {{ limit }} символов",
        maxMessage: "ISBN должен содержать максимум {{ limit }} символов"
    )]
    private ?string $isbn = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $coverImage = null;

    #[ORM\ManyToMany(targetEntity: Author::class, inversedBy: 'books')]
    #[ORM\JoinTable(name: 'book_authors')]
    #[Assert\Count(min: 1, minMessage: "У книги должен быть хотя бы один автор")]
    private Collection $authors;

    public function __construct()
    {
        $this->authors = new ArrayCollection();
    }

    // Геттеры и сеттеры
    public function getId(): ?int { return $this->id; }
    public function getTitle(): ?string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }
    public function getReleaseYear(): ?int { return $this->releaseYear; }
    public function setReleaseYear(int $releaseYear): static { $this->releaseYear = $releaseYear; return $this; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }
    public function getIsbn(): ?string { return $this->isbn; }
    public function setIsbn(string $isbn): static { $this->isbn = $isbn; return $this; }
    public function getCoverImage(): ?string { return $this->coverImage; }
    public function setCoverImage(?string $coverImage): static { $this->coverImage = $coverImage; return $this; }
    public function getAuthors(): Collection { return $this->authors; }
    
    public function addAuthor(Author $author): static
    {
        if (!$this->authors->contains($author)) { $this->authors->add($author); }
        return $this;
    }
    public function removeAuthor(Author $author): static { $this->authors->removeElement($author); return $this; }
}
