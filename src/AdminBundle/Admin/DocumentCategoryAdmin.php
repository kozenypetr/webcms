<?php

// src/AppBundle/Admin/CategoryAdmin.php
namespace AdminBundle\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Form\FormMapper;

use Sonata\CoreBundle\Validator\ErrorElement;
use CmsBundle\Entity\DocumentCategory;

class DocumentCategoryAdmin extends AbstractAdmin
{
  protected function configureFormFields(FormMapper $formMapper)
  {
    $formMapper
      ->with('Základní informace', array('class' => 'col-md-9'))
        ->add('name', 'text', array('label' => 'Název kategorie'))

        ->add('sid', 'text', array('label' => 'Strojový název kategorie'))
        ->add('tags', 'text', array('label' => 'Tagy', 'required' => false))
        ->add('widget', 'text', array('label' => 'Widget', 'required' => false))
        ->add('is_enable_as_parent', 'checkbox', array('label' => 'Povolit jako rodiče', 'required' => false))
      ->end()
      ->with('Zobrazení', array('class' => 'col-md-3'))
        ->add('icon', 'text', array('label' => 'Ikona'))
      ->end();
    ;
    // navod na tiny
    // http://www.techtonet.com/sonata-add-ckeditor-in-admin-textareas/
  }

  protected function configureDatagridFilters(DatagridMapper $datagridMapper)
  {
    $datagridMapper
      ->add('name', null, array('label' => 'Název kategorie'))
    ;
  }

  protected function configureListFields(ListMapper $listMapper)
  {
    $listMapper
      ->addIdentifier('id')
      ->add('name', null, array('label' => 'Název kategorie','editable' => true))
      ->add('sid', null, array('label' => 'Strojový název'))
      ->add('icon', null, array('label' => 'Ikona'))
      ->add('widget', null, array('label' => 'Widget'))
      ->add('is_enable_as_parent', 'boolean', array('label' => 'Rodič','editable' => true))
      // add custom action links
      ->add('_action', 'actions', array(
        'actions' => array(
          'edit' => array(),
          'delete' => array(),
        ),
        'label' => 'Akce'
      ))
    ;
  }


  // add this method
  public function validate(ErrorElement $errorElement, $object)
  {
    $errorElement
      ->with('name')
      ->assertLength(array('max' => 32))
      ->end()
    ;
  }

  public function toString($object)
  {
    return $object instanceof DocumentCategory
      ? $object->getName()
      : 'Kategorie'; // shown in the breadcrumb on the create view
  }

}