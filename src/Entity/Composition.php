<?php

namespace App\Entity;

use App\Repository\RecipeRepository;
use App\Entity\Recipe;
use App\Repository\CompositionRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=CompositionRepository::class)
 * 
 */
class Composition
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"compositions_get_collection", "compositions_get_item"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="compositions")
     * @ORM\JoinColumn(nullable=false)
     * @Ignore
     */
    private $recipe;

    /**
     * @ORM\ManyToOne(targetEntity=Food::class, inversedBy="compositions")
     * @Groups({"compositions_get_collection", "compositions_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $food;

    /**
     * @ORM\Column(type="string", length=20, nullable=true)
     * @Groups({"compositions_get_collection", "compositions_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $unity;

    /**
     * @ORM\Column(type="string",nullable=true)
     * @Groups({"compositions_get_collection", "compositions_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $quantity;

    public function __toString()
    {
        return '' . $this->getFood();
    }


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

    public function getQuantity(): ?string
    {
        return $this->quantity;
    }

    public function setQuantity(string $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
