<?php

// src/AppBundle/Admin/CategoryAdmin.php
namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\CoreBundle\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;


use Sonata\AdminBundle\Route\RouteCollection;

use Sonata\CoreBundle\Validator\ErrorElement;
use CmsBundle\Entity\Gallery;

class GalleryAdmin extends AbstractAdmin
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
        ->add('name', 'text', array('label' => 'Název produktu'))
        // ->add('slug', 'text', array('label' => 'URL'))
        ->add('subname', 'text', array('label' => 'Upřesňující název', 'required' => false))
        ->add('annotation', 'text', array('label' => 'Krátký popis'))
        ->add('description', 'textarea', array('label' => 'Popis', 'required' => false, 'attr' => array('class' => 'tiny')))
        ->add('is_active', 'checkbox', array('label' => 'Aktivní', 'required' => false))
        ->add('sort', 'text', array('label' => 'Řazení', 'required' => true))
      ->end()
      ->with('Kategorie', array('class' => 'col-md-3'))
        ->add('mainCategory', 'sonata_type_model', array('class' => 'CmsBundle\Entity\GalleryCategory', 'label' => 'Hlavní kategorie', 'expanded' => false, 'multiple' => false))
        ->add('categories', 'sonata_type_model', array('class' => 'CmsBundle\Entity\GalleryCategory', 'label' => 'Kategorie galerie', 'expanded' => true, 'multiple' => true))
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
      ->add('categories', null, array('label' => 'Kategorie', 'expand' => true, 'show_filter' => true))
      ->add('locale', 'doctrine_orm_string', array('label' => 'Jazyk', 'show_filter' => true), 'choice',
            ['choices' => ['Čeština' => 'cs', 'Angličtina' => 'en']])
    ;
  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('name', null, array('label' => 'Název produktu','editable' => true))
      ->add('is_active', 'boolean', array('label' => 'Aktivní', 'editable' => true))
      ->add('locale', null, array('label' => 'Jazyk', 'editable' => true))
      ->add('sort', null, array('label' => 'Řazení', 'editable' => true))
      ->add('mainCategory', null, array('label' => 'Hlavní kategorie', 'editable' => false))
      ->add('categories', null, array('label' => 'Kategorie', 'editable' => true))
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