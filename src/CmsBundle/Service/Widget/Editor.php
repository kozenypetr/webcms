<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Validator\Constraints as Assert;

class Editor extends Base
{

    protected $type = 'Editor';

    protected $title = 'Text';

    protected $template = 'editor.html.twig';

    protected $icon = 'fa-file-text-o';

    protected $predefinedClasses = array(
        'Bílý box' => 'white-box',
        'Modrý box' => 'blue-box'
    );

    protected $default = array(
        'html' => "<p>Zde zadejte text</p>",
    );

    protected function configureForm($form)
    {
        return $form
            ->add('html', TextareaType::class, array(
                'label'=>'Text',
                'constraints' => array(new Assert\NotBlank()),
                'attr' => ['class' => 'tiny'],
            ));
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}