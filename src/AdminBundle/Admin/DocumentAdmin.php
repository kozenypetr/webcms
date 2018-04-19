<?php

namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class DocumentAdmin extends AbstractAdmin
{
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('lft')
            ->add('lvl')
            ->add('rgt')
        ;
    }

    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('lft')
            ->add('lvl')
            ->add('rgt')
            ->add('_action', null, [
                'actions' => [
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
                ],
            ])
        ;
    }

    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('lft')
            ->add('lvl')
            ->add('rgt')
        ;
    }

    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('name')
            ->add('url')
            ->add('lft')
            ->add('lvl')
            ->add('rgt')
        ;
    }
}
