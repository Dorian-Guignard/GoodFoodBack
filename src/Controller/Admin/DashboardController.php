<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Composition;
use App\Entity\Food;
use App\Entity\Recipe;
use App\Entity\Virtue;
use App\Repository\FoodRepository;
use App\Repository\RecipeRepository;
use App\Repository\VirtueRepository;
use App\Repository\CategoryRepository;
use App\Repository\CompositionRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Assets;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\Request;



class DashboardController extends AbstractDashboardController
{

    /**
     * @var CategoryRepository
     */
    protected CategoryRepository $categoryRepository;

    /**
     * @var CompositionRepository
     */
    protected CompositionRepository $compositionRepository;

    /**
     * @var FoodRepository
     */
    protected FoodRepository $foodRepository;

    /**
     * @var RecipeRepository 
     */
    protected RecipeRepository $recipeRepository;

    /**
     * @var VirtueRepository 
     */
    protected VirtueRepository $virtueRepository;

    public function __construct(
        VirtueRepository $virtueRepository,
        RecipeRepository $recipeRepository,
        FoodRepository $foodRepository,
        CompositionRepository $compositionRepository,
        CategoryRepository $categoryRepository
    ) {
        $this->virtueRepository = $virtueRepository;
        $this->recipeRepository = $recipeRepository;
        $this->foodRepository = $foodRepository;
        $this->compositionRepository = $compositionRepository;
        $this->categoryRepository = $categoryRepository;
    }


    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render(
            'bundles/EasyAdminBundle/welcome.html.twig',
            [
                'virtue' => $this->virtueRepository->findAll(),
                'recipe' => $this->recipeRepository->findAll(),
                'food' => $this->foodRepository->findAll(),
                'composition' => $this->compositionRepository->findAll(),
                'category' => $this->categoryRepository->findAll(),
            ]
        );
    }

    /**
     * @return Dashboard
     */
    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Projet 12 Recettes Healthy Back');
    }

    /**
     * @return iterable
     */
    public function configureMenuItems(): iterable
    {

        return [
            MenuItem::linkToDashboard('Dashboard', 'fa fa-home'),
            MenuItem::linkToCrud('Composition', 'fa fa-filter', Composition::class),
            MenuItem::linkToCrud('Food', 'fas fa-apple-alt', Food::class),
            MenuItem::linkToCrud('Recipe', 'far fa-lemon', Recipe::class)->setAction('new')
            ,
            MenuItem::linkToCrud('Virtue', 'fas fa-leaf', Virtue::class),
            MenuItem::linkToCrud('Category', 'fas fa-poll-h', Category::class),
            
        ];
    }
}
