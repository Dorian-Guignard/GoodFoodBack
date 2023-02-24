<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use App\Repository\CompositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity(repositoryClass=CompositionRepository::class)
 * @UniqueEntity("name")
 */
class Composition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compositions_get_collection", "virtues_get_item"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="compositions)", 
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"compositions_get_collection", "compositions_get_item"})
     */
    private $recipe;

    /**
     * @ORM\ManyToOne(targetEntity=Food::class, inversedBy="compositions"), 
     * @Groups({"compositions_get_collection", "compositions_get_item"})
     */
    private $food;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"compositions_get_collection", "compositions_get_item"})
     */
    private $unity;

    /**
     * @ORM\Column(type="integer",nullable=true)
     * @Groups({"compositions_get_collection", "compositions_get_item"})
     */
    private $quantity;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRecipe(): ?Recipe
    {
        return $this->recipe;
    }

    public function setRecipe(?Recipe $recipe): self
    {
        $this->recipe = $recipe;

        return $this;
    }

    public function getFood(): ?Food
    {
        return $this->food;
    }

    public function setFood(?Food $food): self
    {
        $this->food = $food;

        return $this;
    }

    public function getUnity(): ?string
    {
        return $this->unity;
    }

    public function setUnity(?string $unity): self
    {
        $this->unity = $unity;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
