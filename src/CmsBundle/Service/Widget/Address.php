<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Validator\Constraints as Assert;

class Address extends Base
{
  protected $type = 'Adresa';

  protected $title = 'Adresa';

  protected $template = 'address.html.twig';

  protected $icon = 'fa-address-card';

  protected $default = array(
      'title'         => 'Nadpis',
      'address'       => 'Adresa',
      'email'         => '',
      'phone'         => '',
      'opening_hours' => '',
  );

  protected function configureForm($form)
  {
      return $form
          ->add('title', TextType::class, array(
              'label' => 'Nadpis'
          ))
          ->add('address', TextType::class, array(
              'label' => 'Adresa'
          ))
          ->add('email', TextType::class, array(
              'label' => 'Email'
          ))
          ->add('phone', TextType::class, array(
              'label' => 'Telefon'
          ))
          ->add('opening_hours', TextType::class, array(
              'label' => 'Otevírací doba'
          ))
          ;
  }


  public function getDefault()
  {
    return array_merge(parent::getDefault(), $this->default);
  }
}