<?php

namespace App\Entity;

use App\Repository\UrlRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UrlRepository::class)]
class Url
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $original = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $shortened = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOriginal(): ?string
    {
        return $this->original;
    }

    public function setOriginal(string $original): static
    {
        $this->original = $original;

        return $this;
    }

    public function getShortened(): ?string
    {
        return $this->shortened;
    }

    public function setShortened(string $shortened): static
    {
        $this->shortened = $shortened;

        return $this;
    }
}
