<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Validator\Constraints as Assert;

class News extends Base
{

  protected $type = 'Novinka';

  protected $title = 'Novinka';

  protected $template = 'new.html.twig';

  protected $icon = 'fa-newspaper-o';

  protected $group = 'Obsah';

    protected $default = array(
        'title' => 'Název aktuality',
        'annotation' => '',
        'text' => '',
        'image' => '',
        'image2' => '',
        'image3' => '',
        'image4' => '',
        'image5' => '',
        'date'  => '',
        'subtitle' => '',
        'document' => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, array(
                'label'=>'Nadpis'
            ))
            ->add('subtitle', TextType::class, ['label' => 'Podnadpis'])
            ->add('annotation', TextareaType::class, array(
                'label'=>'Krátký popis',
            ))
            ->add('text', TextareaType::class, array(
              'label'=>'Text aktuality',
              'constraints' => array(new Assert\NotBlank()),
              'attr' => ['class' => 'tiny'],
            ))
            ->add('date', TextType::class, array(
                'label'=>'Datum'
            ))
            ->add('image', TextType::class, array(
                'label'=>'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('image2', TextType::class, array(
                'label'=>'Obrázek 2',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('image3', TextType::class, array(
                'label'=>'Obrázek 3',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('image4', TextType::class, array(
                'label'=>'Obrázek 4',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('image5', TextType::class, array(
                'label'=>'Obrázek 5',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('document', TextType::class, ['label' => 'Stránka', 'attr' => ['class' => 'droplink']]);
    }

    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        $parameters['link'] = $parameters['document'];
        if (preg_match('/\[(.+):([0-9]+)\]/', $parameters['document'], $matches))
        {
            $documentId = $matches[2];

            // najdeme dokument
            $document = $this->em->getRepository('CmsBundle:Document')->find($documentId);
            $parameters['document'] = $document;

            if ($document)
            {
                $parameters['link'] = '/' . $document->getUrl();
            }
        }

        return $parameters;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}