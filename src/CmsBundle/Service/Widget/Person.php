<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Validator\Constraints as Assert;

class Person extends Base
{

  protected $type = 'Člen týmu';

  protected $title = 'Člen týmu';

  protected $template = 'person.html.twig';

  protected $icon = 'fa-user';

  protected $group = 'Obsah';

    protected $default = array(
        'image' => '',
        'name' => 'Jméno a příjmení',
        'jobtitle' => 'Pozice',
        'annotation' => 'Krátký popis',
        'facebook' => '',
        'twitter' => '',
        'instagram'  => '',
        'telefon' => '',
        'email' => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('name', TextType::class, array(
                'label'=>'Jméno a příjmení',
                'constraints' => array(new Assert\NotBlank()),
            ))
            ->add('image', TextType::class, array(
                'label'=>'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('jobtitle', TextType::class, array(
                'label'=>'Pozice',
                'constraints' => array(new Assert\NotBlank()),
            ))
            ->add('annotation', TextareaType::class, array(
                'label'=>'Krátký popis',
            ))
            ->add('facebook', TextType::class, array(
                'label'=>'Facebook',
            ))
            ->add('twitter', TextType::class, array(
                'label'=>'Twitter',
            ))
            ->add('instagram', TextType::class, array(
                'label'=>'Instagram',
            ))
            ->add('telefon', TextType::class, array(
                'label'=>'Telefon',
            ))
            ->add('email', TextType::class, array(
                'label'=>'Email',
            ))
            ;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}