<?php

namespace AdminBundle\Admin\Article;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class TagAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
        ->with('Základní informace')
          ->add('locale', 'choice',
                ['label' => 'Jazyk', 'required' => false,
                    'choices' => [
                        'Čeština' => 'cs',
                        'Angličtina' => 'en',
                    ]
          ])
          ->add('name', 'text', array('label' => 'Název'))
          ->add('is_active', 'checkbox', array('label' => 'Aktivní', 'required' => false))
          ->add('sort', 'text', array('label' => 'Řazení', 'required' => false))
        ->end()

    ;
  }

  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
        ->add('name', null, array('label' => 'Název kategorie', 'show_filter' => true))
        ->add('locale', 'doctrine_orm_string', array('label' => 'Jazyk', 'show_filter' => true), 'choice',
             ['choices' => ['Čeština' => 'cs', 'Angličtina' => 'en']])
        ;
  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('name', null, array('label' => 'Název kategorie', 'editable' => true, 'label_icon' => 'fa fa-thumbs-o-up'))
      ->add('is_active', 'boolean', array('label' => 'Aktivní', 'editable' => true))
      ->add('sort', null, array('label' => 'Řazení', 'editable' => true))
      ->add('locale', null, array('label' => 'Jazyk', 'editable' => true))
      ->add('_action', 'actions', array(
        'actions' => array(
          'edit' => array(),
          'delete' => array(),
        ),
        'label' => 'Akce'
      ))
    ;
  }
}