<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class ContentController extends Controller
{

    public function sliderAction()
    {

        return $this->render('CmsBundle:Templates/content:slider.html.twig', array(

        ));
    }
}
