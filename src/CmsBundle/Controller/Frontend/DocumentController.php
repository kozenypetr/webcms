<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;


class DocumentController extends Controller
{
    /**
     * @Route("/", name="cms_homepage", defaults={"url": ""},)
     * @Route("/{url}", name="cms_page",
     *     requirements={"url" = ".*"})
     */
    public function showAction($url)
    {
        $criteria = array('url' => $url);

        if (is_null($this->getUser()) ||  !$this->getUser()->hasRole('ROLE_ADMIN'))
        {
            $criteria['status'] = 1;
        }

        $document = $this->getDoctrine()
                ->getRepository('CmsBundle:Document')
                ->findOneBy($criteria);

        if (!$document) {
            throw $this->createNotFoundException('Dokument neexistuje');
        }

        $this->get('cms.manager.content')->setDocument($document);

        if  ($this->getUser() && $this->getUser()->hasRole('ROLE_ADMIN'))
        {
            $pageTemplate = 'page.admin.html.twig';
        }
        else
        {
            $pageTemplate = 'page.html.twig';
        }

        return $this->render('CmsBundle:Editor:page.base.html.twig', array(
            'document' => $document,
            'host'     => $this->get('cms.manager.domain')->getHost(),
            'pageTemplate' => $pageTemplate
        ));
    }
}
