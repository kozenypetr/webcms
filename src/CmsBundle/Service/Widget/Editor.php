<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Validator\Constraints as Assert;

class Editor extends Base
{

    protected $type = 'Editor';

    protected $title = 'HTML';

    protected $template = 'CmsBundle:Widget:editor.html.twig';

    protected $default = array(
        'html' => "<p>Editor</p>",
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