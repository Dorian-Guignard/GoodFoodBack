<?php

namespace App\Entity;
use App\Entity\V;
use App\Entity\Composition;
use App\Repository\RecipeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $duration;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $heatTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $prepTime;

    /**
     * @ORM\Column(type="integer")
     */
    private $portion;

    /**
     * @ORM\Column(type="array")
     */
    private $steps = [];

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $picture;

    /**
     * @ORM\OneToMany(targetEntity=Composition::class, mappedBy="recipe", orphanRemoval=true)
     */
    private $compositions;

    /**
     * @ORM\ManyToOne(targetEntity=Virtue::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $virtue;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
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

    public function getSteps(): ?array
    {
        return $this->steps;
    }

    public function setSteps(array $steps): self
    {
        $this->steps = $steps;

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

 
}
