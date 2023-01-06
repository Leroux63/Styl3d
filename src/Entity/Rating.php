<?php

namespace App\Entity;

use App\Repository\RatingRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RatingRepository::class)]
class Rating
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
//    #[Assert\NotBlank(message:'La valeur ne peut pas être vide')]
//    #[Assert\Length(min: 1, min: 'La note ne peut pas être en dessous de {{ limit }}')]
//    #[Assert\Length(max: 5, max: 'La note ne peut pas être au dessus de{{ limit }}')]
    private ?int $score = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'ratings')]
    private ?Product $product = null;

    #[ORM\ManyToOne(cascade: ['persist'], inversedBy: 'ratings')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
}
