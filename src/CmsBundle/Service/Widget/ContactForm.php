<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use CmsBundle\Entity\Contact;


use Symfony\Component\Validator\Constraints as Assert;

class ContactForm extends Base
{

  protected $type = 'Kontaktní formulář';

  protected $title = 'Kontaktní formulář';

  protected $template = 'contactform.html.twig';

  protected $icon = 'fa-envelope-o';

  protected $group = 'Obsah';

    protected $default = array(
        'title' => 'Nadpis formuláře',
        'sender' => 'info@stavebni-firma-cermak.cz',
        'receiver' => 'info@stavebni-firma-cermak.cz',
        'mailText' => '<p>Dobrý den,<br /><br />děkujeme za dotaz, odpovíme Vám hned jak to bude možné.</p>',
        'document' => ''
    );

    protected function configureForm($form)
    {
        return $form
            ->add('title', TextType::class, array(
                'label'=>'Nadpis formuláře'
            ))
            ->add('sender', TextType::class, array(
                'label'=>'Odesílatel'
            ))
            ->add('receiver', TextType::class, array(
                'label'=>'Příjemce'
            ))
            /*->add('annotation', TextareaType::class, array(
                'label'=>'Krátký popis',
                'constraints' => array(new Assert\NotBlank()),
            ))*/
            ->add('mailText', TextareaType::class, array(
              'label'=>'Krátký popis',
              'constraints' => array(new Assert\NotBlank()),
              'attr' => ['class' => 'tiny'],
            ))
            ->add('document', TextType::class, ['label' => 'Přesměrova na stránku', 'attr' => ['class' => 'droplink']])
            ;
    }


    public function getWidgetParameters($editor = false)
    {
        $parameters = parent::getWidgetParameters();

        $contact = new Contact();

        # Add form fields
        $form = $this->formFactory->createBuilder()
            ->add('name', TextType::class, array('label'=> 'Jméno a příjmení'))
            ->add('email', TextType::class, array('label'=> 'Email'))
            ->add('phone', TextType::class, array('label'=> 'Telefon'))
            ->add('subject', TextType::class, array('label'=> 'Předmět'))
            ->add('message', TextareaType::class, array('label'=> 'Zpráva'))
            ->add('widget_id', HiddenType::class)
            ->add('Save', SubmitType::class, array('label'=> 'Odeslat dotaz', 'attr' => ['class' => 'btn btn-effect', 'id' => 'submit']))
            ->getForm();

        $form->get('widget_id')->setData($this->entity->getId());

        $parameters['form'] = $form->createView();

        return $parameters;
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}