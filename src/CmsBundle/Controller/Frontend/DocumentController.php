<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DocumentController extends Controller
{
    /**
     * @Route("/", name="cms_homepage", defaults={"url": ""},)
     * @Route("/{url}", name="cms_page",
     *     requirements={"url" = ".+"})
     */
    public function showAction($url)
    {
        $document = $this->getDoctrine()
                ->getRepository('CmsBundle:Document')
                ->findOneByUrl($url);

        if (!$document) {
            throw $this->createNotFoundException('Dokument neexistuje');
        }

        $template = 'CmsBundle:Templates/default/Document:main.html.twig';

        return $this->render('CmsBundle:Frontend/Document:show.html.twig', array(
            'document' => $document,
            'template' => $template
        ));
    }
}
