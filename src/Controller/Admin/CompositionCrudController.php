<?php

namespace App\Controller\Admin;

use App\Entity\Composition;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CompositionCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Composition::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Composition')
            ->overrideTemplate('crud/edit', 'bundles/EasyAdminBundle/custom/crud_edit_custom.html.twig')
            ->overrideTemplate('crud/index', 'bundles/EasyAdminBundle/custom/crud_index_custom.html.twig')
            ->overrideTemplate('crud/detail', 'bundles/EasyAdminBundle/custom/crud_detail_custom.html.twig')
            ->overrideTemplate('crud/new', 'bundles/EasyAdminBundle/custom/crud_new_custom.html.twig');
    }
}
