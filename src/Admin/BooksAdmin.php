<?php

declare(strict_types=1);

namespace App\Admin;

use App\Form\AuthorType;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class BooksAdmin extends AbstractAdmin
{

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('title')
            ->add('description')
            ->add('publicationYear')
            ->add('authorsCount');

    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('title')
            ->add('description')
            ->add('publicationYear')
            ->add('authorsCount')
            ->add(ListMapper::NAME_ACTIONS, null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->add('title')
            ->add('description')
            ->add('publicationYear')
            ->add('authors',CollectionType::class, array(
                'entry_type' => AuthorType::class,
                'entry_options' => ['label' => false],
                'allow_add' => true,
                'by_reference' => false
            ));
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->add('title')
            ->add('description')
            ->add('publicationYear', NumberType::class)
            ->add('authorsCount');
    }
}
