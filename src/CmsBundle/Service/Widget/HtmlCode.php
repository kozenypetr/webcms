<?php

namespace CmsBundle\Service\Widget;

use CmsBundle\Service\Widget\Base;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

use Symfony\Component\Validator\Constraints as Assert;

class HtmlCode extends Base
{

    protected $type = 'HTML';

    protected $title = 'HTML';

    protected $template = 'code.html.twig';

    protected $icon = 'fa-file-code-o';

    protected $default = array(
        'html' => "",
    );

    protected function configureForm($form)
    {
        return $form
            ->add('html', TextareaType::class, array(
                'label'=>'Text',
                'constraints' => array(new Assert\NotBlank()),
            ));
    }


    public function getDefault()
    {
        return array_merge(parent::getDefault(), $this->default);
    }
}