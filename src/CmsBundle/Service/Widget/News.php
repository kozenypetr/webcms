<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Validator\Constraints as Assert;

class News extends Base
{

    protected $type = 'Novinka';

  protected $template = 'CmsBundle:Widget:news.html.twig';

    protected $default = array(
        'title' => 'Název aktuality',
        'annotation' => 'Krátký popis aktuality',
        'text' => '<p>Text aktuality</p>'
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title')
            ->add('annotation', TextareaType::class, array(
                'label'=>'Krátký popis',
                'constraints' => array(new Assert\NotBlank()),
            ))
            ->add('text', TextareaType::class, array(
              'label'=>'Krátký popis',
              'constraints' => array(new Assert\NotBlank()),
              'attr' => ['class' => 'tiny'],
            ));
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}