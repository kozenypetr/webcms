<?php

namespace CmsBundle\Controller\Editor;



use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Document;
use CmsBundle\Entity\DocumentCategory;
use CmsBundle\Entity\Widget;

use CmsBundle\Repository\DocumentRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/cms/document")
 */
class DocumentController extends Controller
{
    /**
     * Vytvoreni dokumentu
     * @Route("/add", name="cms_document_add")
     * @Method({"GET", "POST"})
     */
    public function addAction(Request $request)
    {
        $document = new Document();

        $parent = null;
        $parent_id = $request->get('parent_id');

        if ($parent_id) {
            $parent = $this->getDoctrine()
                ->getRepository(Document::class)
                ->find($parent_id);
        }

        $form = $this->createFormBuilder($document)
            ->add('name', TextType::class, array('label' => 'Název'))
            ->add('metatitle', TextType::class, array('label' => 'Metatitle'))
            ->add('metakeywords', TextType::class, array('label' => 'Metakeywords'))
            ->add('metadescription', TextType::class, array('label' => 'Metadescription'))
            ->add('url', TextType::class, array('label' => 'URL'))
            ->add('status', ChoiceType::class,
                array(
                    'label' => 'Stav',
                    'choices'  => array(
                        'Neativní' => 0,
                        'Aktivní'  => 1,
                    )
                )
            )
            ->add('template', ChoiceType::class,
                array(
                    'label'   => 'Šablona',
                    'choices' => $this->get('cms.manager.template')->getDocumentTemplates(),
                    'data'    => $this->get('cms.manager.template')->getDefaultTemplate()
                )
            )
            ->add('parent', EntityType::class, array(
                'label' => 'Nadřazená stránka',
                'class' => Document::class,
                'query_builder' => function (DocumentRepository $er) {
                    return $er->createQueryBuilder('d')
                        ->innerJoin('d.category', 'c', 'WITH', 'c.isEnableAsParent = 1')
                        ->orderBy('d.lft', 'ASC');
                },
                'choice_label' => 'treeName',
                'data' => $parent?$parent:null
            ))
            ->add('category', EntityType::class, array(
                'label' => 'Kategorie dokumentu',
                'class' => DocumentCategory::class,
                'choice_label' => 'name',
            ))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $document = $form->getData();

            $em = $this->getDoctrine()->getManager();

            $em->persist($document);

            $em->flush();

            // pokud ma dokument definovany vychozi widget a widget existuje
            if ($document->getCategory()->getWidget() && $this->has($document->getCategory()->getWidget()))
            {
                $parameters = [
                    'document_id' => $document->getId(),
                    'region' => 'page.content',
                    'widget' => $document->getCategory()->getWidget()
                ];

                $widget = $this->getDoctrine()
                    ->getRepository(Widget::class)
                    ->createWidget($parameters);

                // vychozi nastaveni widgetu
                $widget->setParameters($this->get($widget->getService())->getDefault());
                $widget->setIsSystem(true);

                // ulozime widget
                $em->persist($widget);
                $em->flush();
            }

            return new JsonResponse(array('url' => $document->getUrl()));
        }




        return $this->render('CmsBundle:Editor/Form:document.html.twig', array(
            'form' => $form->createView(),
            'action' => $this->generateUrl('cms_document_add')
        ));
    }


    /**
     * Kopirovani dokumentu
     * @Route("/copy/{id}", name="cms_document_copy")
     * @Method({"GET"})
     */
    public function copyAction(Request $request, Document $document)
    {
        $em = $this->getDoctrine()->getManager();

        $copyDocument = clone $document;

        $parent = $document->getParent();

        if (!$parent)
        {
            $parent = $document;
        }

        $copyDocument->setParent($parent);
        $copyDocument->setUrl($copyDocument->getUrl() . '2');

        $em->persist($copyDocument);

        foreach ($document->getWidgets() as $widget)
        {
            $copyWidget = clone $widget;
            $copyWidget->setDocument($copyDocument);

            $em->persist($copyWidget);
        }

        $em->flush();

        return $this->redirect('/' . $copyDocument->getUrl());
    }

    /**
     * @Route("/delete/{id}", name="cms_document_delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @param Document $document
     * @return JsonResponse
     */
    public function deleteAction(Request $request, Document $document)
    {
        $document_id = $document->getId();

        $em = $this->getDoctrine()->getManager();

        $em->remove($document);

        /*$this->getDoctrine()
             ->getRepository(Document::class)
             ->removeFromTree($document);*/

        $em->flush();

        return new JsonResponse(array('status' => 'SUCCESS', 'document_id' => $document_id));
    }


    /**
     * Editace dokumentu
     * @Route("/edit/{id}", name="cms_document_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Document $document)
    {
        $formBuilder = $this->createFormBuilder($document)
            ->add('name', TextType::class, array('label' => 'Název'))
            ->add('metatitle', TextType::class, array('label' => 'Metatitle'))
            ->add('metakeywords', TextType::class, array('label' => 'Metakeywords'))
            ->add('metadescription', TextType::class, array('label' => 'Metadescription'))
            ->add('template', ChoiceType::class,
                array(
                    'label' => 'Šablona',
                    'choices' => $this->get('cms.manager.template')->getDocumentTemplates()
                )
            );


        if ($document->getUrl())
        {
            $formBuilder
                ->add('parent', EntityType::class, array(
                    'label' => 'Nadřazená stránka',
                    'class' => Document::class,
                    'query_builder' => function (DocumentRepository $er) {
                        return $er->createQueryBuilder('d')
                            ->innerJoin('d.category', 'c', 'WITH', 'c.isEnableAsParent = 1')
                            ->orderBy('d.lft', 'ASC');
                    },
                    'choice_label' => 'treeName',
                    'choice_attr' => function($val, $key, $index) use ($document) {
                        // adds a class like attending_yes, attending_no, etc
                        return ($document->getId() == $val->getId())?['disabled' => 'disabled']:[];
                    }
                ))
                ->add('url', TextType::class, array('label' => 'URL'))
                ->add('status', ChoiceType::class,
                    array(
                        'label' => 'Stav',
                        'choices'  => array(
                            'Neativní' => 0,
                            'Aktivní'  => 1,
                        )
                    )
                )
                ->add('category', EntityType::class, array(
                    'label' => 'Kategorie dokumentu',
                    'class' => DocumentCategory::class,
                    'choice_label' => 'name',
                ));
        }
        else
        {
            $formBuilder
                ->add('parent', HiddenType::class)
                ->add('url', TextType::class, array('label' => 'URL', 'disabled' => 'disabled'))
                ->add('status', TextType::class, array('label' => 'Stav', 'disabled' => 'disabled'))
                ->add('category', EntityType::class, array(
                    'label' => 'Kategorie dokumentu',
                    'class' => DocumentCategory::class,
                    'choice_label' => 'name',
                    'disabled' => 'disabled'
                ));
        }

        $form  = $formBuilder->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $document = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($document);
            $em->flush();

            return new JsonResponse(array('url' => $document->getUrl()));
        }

        return $this->render('CmsBundle:Editor/Form:document.html.twig', array(
            'form' => $form->createView(),
            'document' => $document,
            'action' => $this->generateUrl('cms_document_edit',
                        array('id' => $document->getId()))
        ));
    }

    /**
     * Presunuti dokumentu
     * @Route("/move/{id}/{parent_id}", name="cms_document_move", options={"expose"=true})\
     * @ParamConverter("parent", class="CmsBundle:Document", options={
     *    "repository_method" = "find",
     *    "mapping": {"parent_id": "id"},
     *    "map_method_signature" = true
     * })
     * @Method({"POST"})
     */
    public function moveAction(Request $request, Document $document, Document $parent)
    {
        $em = $this->getDoctrine()->getManager();

        $position = $request->get('position');

        if ($position == 0)
        {
            $operation = 'moveup';
        }
        else
        {
            $children = $parent->getChildren();
            if (!$children)
            {
                $operation = 'moveup';
            }
            else
            {
                $ids = [];
                $i = 1;
                foreach ($children as $item)
                {
                    if ($item->getId() == $document->getId())
                    {
                        continue;
                    }
                    $ids[$i] = $item;
                    $i++;
                }
                $sibling = $ids[$position];
                $operation = 'movenext';
            }
        }

        $repo = $em->getRepository('CmsBundle:Document');

        switch ($operation)
        {
            case 'moveup':
                dump($document);
                dump($parent);
                $repo->persistAsFirstChildOf($document, $parent);
            break;
            case 'movenext':
                $repo->persistAsNextSiblingOf($document, $sibling);
            break;
        }

        $em->flush();

        return new JsonResponse(array('status' => 'OK', 'position' => $position));
    }

    /**
     * Seznam dokumentu
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('CmsBundle:Document');

        $hierarchy = $repo->childrenHierarchy(null, false, array());

        return $this->render('CmsBundle:Editor/Sidebar:list.html.twig', array(
            'hierarchy' => $hierarchy
        ));
    }
}
