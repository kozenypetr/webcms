<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Validator\Constraints as Assert;

class Reference extends Base
{

  protected $type = 'Reference';

  protected $title = 'Reference';

  protected $template = 'reference.html.twig';

  protected $icon = ' fa-star';

  protected $group = 'Obsah';

    protected $default = array(
        'title' => 'Název reference',
        'annotation' => '',
        'text' => '<p>Popis reference</p>',
        'image' => '',
        'date'  => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, array(
                'label'=>'Nadpis'
            ))
            ->add('annotation', TextareaType::class, array(
                'label'=>'Krátký popis',
            ))
            ->add('text', TextareaType::class, array(
              'label'=>'Krátký popis',
              'constraints' => array(new Assert\NotBlank()),
              'attr' => ['class' => 'tiny'],
            ))
            ->add('date', TextType::class, array(
                'label'=>'Datum',
                'attr' => ['class' => 'date'],
            ))
            ->add('image', TextType::class, array(
                'label'=>'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ));
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}