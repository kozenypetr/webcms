<?php

namespace CmsBundle\Controller\Frontend;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use Symfony\Component\HttpFoundation\Request;

class DocumentController extends Controller
{
    /**
     * @Route("/fotogalerie/{url}", name="cms_gallery_category")
     */
    public function galleryCategoryAction(Request $request, $url, $category = null)
    {
        // dump($request->get('_route'));

        $criteria = array('slug' => $url, 'isActive' => 1);

        // dotaz na dokument do databaze
        $document = $this->getDoctrine()
                        ->getRepository('CmsBundle:GalleryCategory')
                        ->findOneBy($criteria);

        if (!$document) {
            throw $this->createNotFoundException('Kategorie neexistuje');
        }

        // $pageTemplate = ($this->getUser() && $this->getUser()->hasRole('ROLE_ADMIN'))?'page.admin.html.twig':'page.html.twig';
        $pageTemplate = 'page.html.twig';

        $parentDocument = $this->getDoctrine()
                            ->getRepository('CmsBundle:Document')
                            ->find($this->getParameter('gallery_document_id'));

        $host = $this->get('cms.manager.domain')->getHost();

        $document->setDocument($parentDocument);

        return $this->render('CmsBundle:Editor:page.base.html.twig', array(
            'document' => $document,
            'host'     => $host,
            'pageTemplate' => $pageTemplate,
            'template' => "CmsBundle::Templates/" . $host . "/Gallery/category.html.twig"
        ));
    }


    /**
     * @Route("/fotogalerie/{category}/{url}", name="cms_gallery_detail")
     */
    public function galleryAction(Request $request, $url, $category)
    {
        $criteria = array('slug' => $category, 'isActive' => 1);

        // dotaz na dokument do databaze
        $category = $this->getDoctrine()
            ->getRepository('CmsBundle:GalleryCategory')
            ->findOneBy($criteria);

        if (!$category) {
            throw $this->createNotFoundException('Kategorie neexistuje');
        }

        $criteria = array('slug' => $url, 'isActive' => 1, 'mainCategory' => $category);

        // dotaz na dokument do databaze
        $document = $this->getDoctrine()
            ->getRepository('CmsBundle:Gallery')
            ->findOneBy($criteria);

        if (!$document) {
            throw $this->createNotFoundException('Galerie neexistuje');
        }

        // $pageTemplate = ($this->getUser() && $this->getUser()->hasRole('ROLE_ADMIN'))?'page.admin.html.twig':'page.html.twig';
        $pageTemplate = 'page.html.twig';

        $parentDocument = $this->getDoctrine()
            ->getRepository('CmsBundle:Document')
            ->find($this->getParameter('gallery_document_id'));

        $host = $this->get('cms.manager.domain')->getHost();

        $document->setDocument($parentDocument);

        return $this->render('CmsBundle:Editor:page.base.html.twig', array(
            'document' => $document,
            'host'     => $host,
            'pageTemplate' => $pageTemplate,
            'template' => "CmsBundle::Templates/" . $host . "/Gallery/detail.html.twig"
        ));
    }

    /**
     * @param $url
     *
     * @return \Symfony\Component\HttpFoundation\Response|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function matchArticleUrl($url)
    {
        // pokusime se najit kategorii
        $path = explode('/', $url);

        // vezmeme posleni cast cesty
        $last = array_pop($path);

        $criteria = array('slug' => $last);
        if (count($path) > 0)
        {
            $criteria['parentUrl'] = join('/', $path);
        }

        $repo = $this->getDoctrine()
                ->getRepository('CmsBundle:Article\Category');

        // dotaz na dokument do databaze
        $document = $repo->findOneBy($criteria);

        if ($document) {

            $template = $document->getTemplateList() ? $document->getTemplateList() : 'list.default.html.twig';

            return $this->render('CmsBundle:Editor:page.base.html.twig', [
                'document' => $document,
                'host' => $this->get('cms.manager.domain')->getHost(),
                'template' => $this->get('cms.manager.template')->getTemplate($template, 'Article')
            ]);
        }

        if (is_null($document))
        {
            $repo = $this->getDoctrine()
                ->getRepository('CmsBundle:Article\Item');

            $document = $repo->findItem($url);

            if ($document) {

                $template = $document->getCategory()
                    ->getTemplateDetail() ? $document->getCategory()
                    ->getTemplateDetail() : 'detail.default.html.twig';

                $this->get('cms.manager.content')->setDocument($document);

                return $this->render('CmsBundle:Editor:page.base.html.twig', [
                    'document' => $document,
                    'host' => $this->get('cms.manager.domain')->getHost(),
                    'template' => $this->get('cms.manager.template')
                        ->getTemplate($template, 'Article')
                ]);
            }
        }

        return null;
    }


    /**
     * @Route("/", name="cms_homepage", defaults={"url": ""},)
     * @Route("/{url}", name="cms_page",
     *     requirements={"url" = ".*"})
     */
    public function showAction($url)
    {
        // zjistim, jestli se nejedna o clanek
        $response = $this->matchArticleUrl($url);

        if (!is_null($response))
        {
            return $response;
        }

        $criteria = array('url' => $url);
        // pokud nejsem admin, tak pridame kriterium na stav dokumentu
        if (is_null($this->getUser()) ||  !$this->getUser()->hasRole('ROLE_ADMIN'))
        {
          $criteria['status'] = 1;
        }
        // dotaz na dokument do databaze
        $document = $this->getDoctrine()
                ->getRepository('CmsBundle:Document')
                ->findOneBy($criteria);

        if (!$document) {
            throw $this->createNotFoundException('Dokument neexistuje');
        }

        $this->get('cms.manager.content')->setDocument($document);

        return $this->render('CmsBundle:Editor:page.base.html.twig', array(
            'document' => $document,
            'host'     => $this->get('cms.manager.domain')->getHost(),
            'template' => $document->getTemplate()
        ));
    }
}
