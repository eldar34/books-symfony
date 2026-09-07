<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\SubscriberRepository::class)]
#[ORM\Table(name: 'subscribers')]
class Subscriber
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Email обязателен")]
    #[Assert\Email(message: "Неверный формат Email")]
    private ?string $email = null;

    #[ORM\ManyToOne(targetEntity: Author::class, inversedBy: 'subscribers')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private ?Author $author = null;

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }
    public function getAuthor(): ?Author { return $this->author; }
    public function setAuthor(?Author $author): static { $this->author = $author; return $this; }
}
