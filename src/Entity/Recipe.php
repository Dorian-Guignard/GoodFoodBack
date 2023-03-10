<?php

namespace App\Entity;

use App\Entity\User;
use App\Entity\Virtue;
use App\Entity\Composition;
use App\Entity\Steps;
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
use App\Controller\Api\RecipeController;
use PhpParser\Node\Expr\New_;
use Vich\UploaderBundle\Util\FilenameUtils;

/**
 * @ORM\Entity(repositoryClass=RecipeRepository::class)
 */
class Recipe
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     */
    private $id;

    /**
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank
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
     * @Assert\NotBlank
     */
    private $duration;

    /**
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank
     */
    private $heatTime;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $prepTime;

    /**
     * @ORM\Column(type="integer")
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $portion;


    /**
     * @ORM\OneToMany(targetEntity=Composition::class, mappedBy="recipe", orphanRemoval=true, cascade={"persist", "remove"})
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $compositions;

    /**
     * @ORM\ManyToOne(targetEntity=Virtue::class, inversedBy="recipes"))
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $virtue;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="recipes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $category;

    /** 
     * @ORM\Column(type="string", nullable=true)
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * 
     */
    private $nameImage;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="recipes")
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * 
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity=Steps::class, mappedBy="recipe", cascade={"persist", "remove"})
     * @Groups({"recipes_get_collection", "recipes_get_item"})
     * @Assert\NotBlank
     */
    private $steps;

    public function __construct()
    {
        $this->compositions = new ArrayCollection();
        $this->steps = new ArrayCollection();
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



    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, Steps>
     */
    public function getSteps(): Collection
    {
        return $this->steps;
    }

    public function addStep(Steps $step): self
    {
        if (!$this->steps->contains($step)) {
            $this->steps[] = $step;
            $step->setRecipe($this);
        }

        return $this;
    }

    public function removeStep(Steps $step): self
    {
        if ($this->steps->removeElement($step)) {
            // set the owning side to null (unless already changed)
            if ($step->getRecipe() === $this) {
                $step->setRecipe(null);
            }
        }

        return $this;
    }

       public function setNameImage(string $nameImage): self
    {
        $this->nameImage = $nameImage;

        return $this;
    }

    public function getNameImage(): ?string
    {
        return $this->nameImage;
    }
}
