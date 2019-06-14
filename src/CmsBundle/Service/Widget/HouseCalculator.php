<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use CmsBundle\Entity\DocumentCategory;


use Symfony\Component\Validator\Constraints as Assert;

class HouseCalculator extends Base
{

    protected $type = 'Kalkulačka domu';

    protected $title = 'Kalkulačka domu';

    protected $template = 'house_calculator.html.twig';

    protected $icon = 'fa-calculator';

    protected $group = 'Obsah';

    protected $predefinedClasses = array(

    );

    protected $default = array(
        'bungalov_cena_zaklad'      => 20000,
        'bungalov_cena_standard'    => 25000,
        'bungalov_cena_nadstandard'  => 28000,
        'patrovy_cena_zaklad'       => 30000,
        'patrovy_cena_standard'     => 35000,
        'patrovy_cena_nadstandard'  => 38000,
    );

    protected function configureForm($form)
    {
        return $form
            ->add('bungalov_cena_zaklad', NumberType::class, ['label' => 'Bungalov cena - základ'])
            ->add('bungalov_cena_standard', NumberType::class, ['label' => 'Bungalov cena - standard'])
            ->add('bungalov_cena_nadstandard', NumberType::class, ['label' => 'Bungalov cena - nadstandard'])
            ->add('patrovy_cena_zaklad', NumberType::class, ['label' => 'Patrový cen - základ'])
            ->add('patrovy_cena_standard', NumberType::class, ['label' => 'Patrový cen - standard'])
            ->add('patrovy_cena_nadstandard', NumberType::class, ['label' => 'Patrový cen - nadstandard'])
            ;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }

}