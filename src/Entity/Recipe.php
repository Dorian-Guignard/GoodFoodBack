<?php

namespace App\Entity;

use App\Entity\Virtue;
use App\Entity\Composition;
use App\Repository\RecipeRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\VirtueRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;
use Symfony\Component\Validator\Constraints;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 * @UniqueEntity("name")
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * 
     */
    private $id;

    /**
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="text" , nullable=true)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     */
    private $description;

    /**
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @ORM\Column(type="integer", nullable=true)
     */
    private $heatTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     */
    private $prepTime;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     */
    private $portion;

   

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Composition::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist", "remove"})
     * @Ignore()
     * @Groups({"recipes_get_collection"})
     */
    private $compositions;

    /**
     * @ORM\ManyToOne(targetEntity=Virtue::class, inversedBy="recipes"))
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"recipes_get_collection"})
     * 
     */
    private $virtue;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"recipes_get_collection"})
     */
    private $category;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $steps = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recipes")
     */
    private $user;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
        /*         $this->virtue = new ArrayCollection();
        $this->category = new ArrayCollection(); */
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

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getHeatTime(): ?int
    {
        return $this->heatTime;
    }

    public function setHeatTime(?int $heatTime): self
    {
        $this->heatTime = $heatTime;

        return $this;
    }

    public function getPrepTime(): ?int
    {
        return $this->prepTime;
    }

    public function setPrepTime(?int $prepTime): self
    {
        $this->prepTime = $prepTime;

        return $this;
    }

    public function getPortion(): ?int
    {
        return $this->portion;
    }

    public function setPortion(int $portion): self
    {
        $this->portion = $portion;

        return $this;
    }
  

    public function getPicture(): ?string
    {
        return $this->picture;
    }

    public function setPicture(?string $picture): self
    {
        $this->picture = $picture;

        return $this;
    }

    public function getVirtue(): ?Virtue
    {
        return $this->virtue;
    }

    public function setVirtue(?Virtue $virtue): self
    {
        $this->virtue = $virtue;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }


    /**
     * @return Collection<int, Composition>
     */
    public function getCompositions(): Collection
    {
        return $this->compositions;
    }

    public function addComposition(Composition $composition): self
    {
        if (!$this->compositions->contains($composition)) {
            $this->compositions[] = $composition;
            $composition->setRecipe($this);
        }

        return $this;
    }

    public function removeComposition(Composition $composition): self
    {
        if ($this->compositions->removeElement($composition)) {
            // set the owning side to null (unless already changed)
            if ($composition->getRecipe() === $this) {
                $composition->setRecipe(null);
            }
        }

        return $this;
    }

    public function getSteps(): ?array
    {
        return $this->steps;
    }

    public function setSteps(?array $steps): self
    {
        $this->steps = $steps;

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
