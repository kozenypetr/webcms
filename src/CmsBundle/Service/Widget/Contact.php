<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;


use Symfony\Component\Validator\Constraints as Assert;

class Contact extends Base
{

  protected $type = 'Kontakt';

  protected $title = 'Kontakt';

  protected $template = 'contact.html.twig';

  protected $icon = 'fa-phone';

    protected $default = array(
        'email' => 'jmeno@email.cz',
        'phone' => '(+420)77712345678',
        'opening_hours' => 'Po-Pá (9h - 16h)',
    );

    protected function configureForm($form)
    {
        return $form
            ->add('email', TextType::class, array(
                'label'=>'Email'
            ))
            ->add('phone', TextType::class, array(
                'label'=>'Telefon'
            ))
            ->add('opening_hours', TextType::class, array(
                'label'=>'Otevírací doba'
            ))
            ;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}