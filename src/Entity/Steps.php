<?php

namespace App\Entity;

use App\Repository\StepsRepository;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity(repositoryClass=StepsRepository::class)
 */
class Steps
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"steps_get_collection", "steps_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=20)
     * @Assert\NotBlank
     * @Groups({"steps_get_collection", "steps_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank
     * @Groups({"steps_get_collection", "steps_get_item","recipes_get_collection", "recipes_get_item" })
     */
    private $content;

    /**
     * @ORM\ManyToOne(targetEntity=Recipe::class, inversedBy="steps")
     * @ORM\JoinColumn(nullable=false)
     * @Ignore()
     */
    private $recipe;

    public function __toString()
    {
        return '' . $this->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?int
    {
        return $this->name;
    }

    public function setName(int $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
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
}
