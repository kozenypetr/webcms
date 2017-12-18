<?php

namespace CmsBundle\Controller\Backend;


use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Box;
use CmsBundle\Form\BoxType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;


class DocumentController extends Controller
{
    /**
     * @Route("/cms-edit/widget/{id}", name="cms_edit_widget")
     */
    public function widgetAction(Request $request, Widget $widget)
    {
        /*$form = $this->createForm(WidgetType::class, $widget, array(
            'action' => $this->generateUrl('cms_edit_widget', array('id' => $widget->getId())),
            'method' => 'POST',
        ));*/

        $service = $this->get($widget->getService());

        $form = $service->setParameters($widget->getParameters())
                        ->createForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $widget->setParameters($form->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($widget);
            $em->flush();

            $html = $this->get('templating')->render($service->getTemplate(), array('widget' => $widget, 'ajax' => true));

            return new JsonResponse(array('html' => $html, 'class' => $widget->getClass()));
        }

        $statusCode = 200;

        if ($form->isSubmitted() && !$form->isValid()){
            $statusCode = 400;
            dump($form->getErrors(true, false));
        }

        return $this->render('CmsBundle:Backend/Document:widget.html.twig', array(
            'widget' => $widget,
            'form' => $form->createView()
        ), new Response('', $statusCode));
    }


    /**
     * @Route("/cms-add/widget/{id}", name="cms_add_widget")
     */
    public function widgetAddAction(Request $request, Box $box)
    {
        $widget = new Widget();

        $widget->setBox($box);
        $widget->setTag('div');
        $widget->setHtml('');
        $widget->setSort(10);
        $widget->setParameters($this->get('cms.widget.editor')->getDefault());
        $widget->setService('cms.widget.editor');

        $em = $this->getDoctrine()->getManager();
        $em->persist($widget);
        $em->flush();

        $widgetHtml = $this->get('templating')->render($this->get('cms.widget.editor')->getTemplate(), array('widget' => $widget));

        return new JsonResponse(array('id' => $widget->getId(), 'widgetHtml' => $widgetHtml));
    }


    /**
     * @Route("/cms-edit/box/{id}", name="cms_edit_box")
     */
    public function boxAction(Request $request, Box $box)
    {
        $form = $this->createForm(BoxType::class, $box, array(
            'action' => $this->generateUrl('cms_edit_box', array('id' => $box->getId())),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $box = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($box);
            $em->flush();

            return new JsonResponse($box->toArray());
        }

        $form->add('submit', SubmitType::class, array(
            'label' => 'Uložit',
            'attr'  => array('class' => 'btn btn-default pull-right')
        ));

        return $this->render('CmsBundle:Backend/Document:box.html.twig', array(
            'box' => $box,
            'form' => $form->createView()
        ));
    }

    public function boxSaveAction()
    {

    }
}
