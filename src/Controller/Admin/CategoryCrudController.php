<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CategoryCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Category::class;
    }


    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Category')
            ->overrideTemplate('crud/edit', 'bundles/EasyAdminBundle/custom/crud_edit_custom.html.twig')
            ->overrideTemplate('crud/index', 'bundles/EasyAdminBundle/custom/crud_index_custom.html.twig')
            ->overrideTemplate('crud/detail', 'bundles/EasyAdminBundle/custom/crud_detail_custom.html.twig')
            ->overrideTemplate('crud/new', 'bundles/EasyAdminBundle/custom/crud_new_custom.html.twig');
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id', 'ID')->onlyOnIndex(),
            TextField::new('name'),
            TextField::new('picture'),
        ];
    }
}
