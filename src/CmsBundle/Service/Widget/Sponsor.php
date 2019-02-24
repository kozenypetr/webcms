<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use CmsBundle\Entity\DocumentCategory;


use Symfony\Component\Validator\Constraints as Assert;

class Sponsor extends Base
{

    protected $type = 'Sponzor';

    protected $title = 'Sponzor';

    protected $template = 'sponsor.html.twig';

    protected $icon = 'fa-money';

    protected $group = 'Obsah';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'title'       => '',
        'image'       => '',
        'annotation'  => '',
        'subtitle'    => '',
        'link'        => '',
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, ['label' => 'Název'])
            ->add('annotation', TextareaType::class, ['label' => 'Popis'])
            ->add('image', TextType::class, array(
                'label' => 'Obrázek',
                'attr' => ['class' => 'dropimage'],
            ))
            ->add('link', TextType::class, ['label' => 'Web'])
            ->add('subtitle', TextType::class, ['label' => 'Podnadpis']);
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }

}