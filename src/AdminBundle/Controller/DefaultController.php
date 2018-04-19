<?php

namespace AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sonata\AdminBundle\Admin\AdminInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/filemanager", name="cms_admin_filemanager")
     */
    public function filemanagerAction()
    {
        return $this->render('AdminBundle:Default:filemanager.html.twig');
    }
}
