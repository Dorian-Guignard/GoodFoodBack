<?php

namespace App\Entity;

use App\Entity\Recipe;
use App\Repository\VirtueRepository;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints;


/**
 * @ORM\Entity(repositoryClass=VirtueRepository::class)
 */
class Virtue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"virtues_get_collection", "virtues_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
     * @Groups({"virtues_get_collection", "virtues_get_item", "recipes_get_collection", "recipes_get_item"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"virtues_get_collection", "virtues_get_item"})
     * @Assert\NotBlank
     */
    private $description;

    /**
     * @ORM\OneToMany(targetEntity=Recipe::class, mappedBy="virtue")
     * @Ignore()
     * 
     */
    private $recipes;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"virtues_get_collection", "virtues_get_item"})
     * 
     */
    private $nameImage;

    public function __construct()
    {
        $this->recipes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getNameImage(): ?string
    {
        return $this->nameImage;
    }

    public function setNameImage(?string $nameImage): self
    {
        $this->nameImage = $nameImage;

        return $this;
    }

    /**
     * @return Collection<int, Recipe>
     */
    public function getRecipes(): Collection
    {
        return $this->recipes;
    }

    public function addRecipe(Recipe $recipe): self
    {
        if (!$this->recipes->contains($recipe)) {
            $this->recipes[] = $recipe;
            $recipe->setVirtue($this);
        }

        return $this;
    }

    public function removeRecipe(Recipe $recipe): self
    {
        if ($this->recipes->removeElement($recipe)) {
            // set the owning side to null (unless already changed)
            if ($recipe->getVirtue() === $this) {
                $recipe->setVirtue(null);
            }
        }

        return $this;
    }
}
