<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DocumentController extends Controller
{
    /**
     * @Route("/", name="cms_homepage", defaults={"url": ""},)
     * @Route("/{url}", name="cms_page")
     */
    public function showAction($url)
    {
        $document = $this->getDoctrine()
                ->getRepository('CmsBundle:Document')
                ->findOneByUrl($url);

        return $this->render('CmsBundle:Frontend/Templates:onecolumn.html.twig', array(
            'document' => $document
        ));
    }
}
