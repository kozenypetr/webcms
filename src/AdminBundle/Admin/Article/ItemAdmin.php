<?php

// src/AppBundle/Admin/CategoryAdmin.php
namespace AdminBundle\Admin\Article;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

use Sonata\AdminBundle\Route\RouteCollection;

use Sonata\CoreBundle\Validator\ErrorElement;
use CmsBundle\Entity\Gallery;

class ItemAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
      ->with('Základní informace', array('class' => 'col-md-9'))
        ->add('locale', 'choice',
            ['label' => 'Jazyk', 'required' => false,
                'choices' => [
                    'Čeština' => 'cs',
                    'Angličtina' => 'en',
                ]
            ])
        ->add('name', 'text', array('label' => 'Název'))
        // ->add('slug', 'text', array('label' => 'URL'))
        ->add('subname', 'text', array('label' => 'Upřesňující název', 'required' => false))
        ->add('dateDisplay', 'sonata_type_date_picker', array('label' => 'Datum', 'required' => false))
        ->add('datePublish', 'sonata_type_date_picker', array('label' => 'Datum publikace', 'required' => false))
        ->add('annotationList', 'text', array('label' => 'Krátký popis výpis', 'required' => false))
        ->add('annotation', 'text', array('label' => 'Krátký popis', 'required' => false))
        ->add('description', 'textarea', array('label' => 'Text', 'required' => false, 'attr' => array('class' => 'tiny')))
        ->add('text', 'textarea', array('label' => 'Druhý text', 'required' => false, 'attr' => array('class' => 'tiny')))
        ->add('is_active', 'checkbox', array('label' => 'Aktivní', 'required' => false))
        ->add('sort', 'text', array('label' => 'Řazení', 'required' => true))
      ->end()
      ->with('Kategorie', array('class' => 'col-md-3'))
        ->add('category', 'sonata_type_model', array('class' => 'CmsBundle\Entity\Article\Category', 'label' => 'Kategorie'))
        ->add('tags', 'sonata_type_model', array('class' => 'CmsBundle\Entity\Article\Tag', 'label' => 'Tagy', 'expanded' => true, 'multiple' => true))
      ->end()
      ->with('Metadata', array('class' => 'col-md-9'))
        ->add('customMetatitle', 'text', array('label' => 'Metatitle', 'required' => false))
        ->add('customMetadescription', 'text', array('label' => 'Metadescription', 'required' => false))
        ->add('customMetakeywords', 'text', array('label' => 'Metakeywords', 'required' => false))
      ->end()
    ;
    // navod na tiny
    // http://www.techtonet.com/sonata-add-ckeditor-in-admin-textareas/
  }

  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('name', null, array('label' => 'Název produktu'))
      ->add('category', null, array('label' => 'Kategorie', 'expand' => true, 'show_filter' => true))
      ->add('locale', 'doctrine_orm_string', array('label' => 'Jazyk', 'show_filter' => true), 'choice',
            ['choices' => ['Čeština' => 'cs', 'Angličtina' => 'en']])
    ;
  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('name', null, array('label' => 'Název produktu','editable' => true))
      ->add('slug', null, array('label' => 'URL'))
      ->add('dateDisplay', null, array('label' => 'Datum'))
      ->add('is_active', 'boolean', array('label' => 'Aktivní', 'editable' => true))
      ->add('locale', null, array('label' => 'Jazyk', 'editable' => true))
      ->add('sort', null, array('label' => 'Řazení', 'editable' => true))
      ->add('category', null, array('label' => 'Kategorie', 'editable' => false))
      ->add('tags', null, array('label' => 'Tagy', 'editable' => true))
      // add custom action links
      ->add('_action', 'actions', array(
        'actions' => array(
          'edit' => array(),
          'delete' => array(),
            'clone' => [
                'template' => 'AdminBundle::CRUD/list__action_clone.html.twig'
            ]
        ),
        'label' => 'Akce'
      ))
    ;
  }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->add('clone', $this->getRouterIdParameter().'/clone');
    }


  // add this method
  public function validate(ErrorElement $errorElement, $object)
  {
    $errorElement
      ->with('name')
      ->assertLength(array('max' => 64))
      ->end()
    ;
  }

  public function toString($object)
  {
    return $object instanceof Product
      ? $object->getName()
      : 'Galerie'; // shown in the breadcrumb on the create view
  }

}