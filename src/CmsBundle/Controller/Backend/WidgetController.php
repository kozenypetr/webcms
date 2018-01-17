<?php

namespace CmsBundle\Controller\Backend;


use CmsBundle\Entity\Document;
use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Box;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;


/**
 * @Route("/cms/widget")
 */
class WidgetController extends Controller
{
    /**
     * Vlozeni widgetu do regionu
     * @Route("/add", name="cms_widget_add")
     * @Method({"PUT"})
     */
    public function addAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // parametry vlozeni - region, typ region [page/global], dokument, prev, next
        $parameters = json_decode($request->get('parameters'), true);

        // vytvorime objekt widgetu
        $widget = $this->getDoctrine()
                       ->getRepository(Widget::class)
                       ->createWidget($parameters);

        // vychozi nastaveni widgetu
        $widget->setParameters($this->get($widget->getService())->getDefault());

        // ulozime widget
        $em->persist($widget);
        $em->flush();

        $service = $this->get($parameters['widget']);

        // ziskame HTML widgetu
        $widgetHtml = $this->get('templating')->render($service->getTemplate(), array('widget' => $widget, 'title' => $service->getTitle()));

        // vracime JSON - nove ID + HTML
        return new JsonResponse(array('id' => $widget->getId(), 'widgetHtml' => $widgetHtml));
    }


    /**
     * Serazeni widgetu v regionu
     * @Route("/sort", name="cms_widget_sort")
     * @Method({"POST"})
     */
    public function sortAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $parameters = json_decode($request->get('parameters'), true);

        $repository = $this->getDoctrine()->getRepository(Widget::class);

        $widget = $repository->find($parameters['widget_id']);

        $sort = $repository->sortWidgetsInRegion(
            $parameters['region'],
            $parameters['document_id'],
            isset($parameters['prev'])?$parameters['prev']:null,
            isset($parameters['next'])?$parameters['next']:null
        );

        $widget->setSort($sort);
        $widget->setRegion($parameters['region']);
        if (preg_match('/^page\./', $parameters['region']))
        {
            $document = $this->getDoctrine()->getRepository(Document::class)->find($parameters['document_id']);
            $widget->setDocument($document);
        }
        else
        {
            $widget->setDocument(null);
        }

        $em->persist($widget);
        $em->flush();

        return new JsonResponse(array('id' => $widget->getId()));
    }

    /**
     * Editace widgetu
     * @Route("/edit/{id}", name="cms_widget_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Widget $widget)
    {
        $service = $this->get($widget->getService());

        $form = $service->setParameters($widget->getParameters())
            ->createForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $widget->setParameters($form->getData());

            $em = $this->getDoctrine()->getManager();
            $em->persist($widget);
            $em->flush();

            $html = $this->get('templating')->render($service->getTemplate(), array('widget' => $widget, 'title' => $service->getTitle(), 'ajax' => true));

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
     * Odstraneni widgetu
     * @Route("/delete/{id}", name="cms_widget_delete")
     * @Method({"DELETE"})
     * @param Request $request
     * @param Widget $widget Widget urceny ke smazani
     * @return JSON
     */
    public function deleteAction(Request $request, Widget $widget)
    {
        try {
            $em = $this->getDoctrine()->getManager();

            $em->remove($widget);

            $em->flush();

            return new JsonResponse(array('status' => 'OK'));
        }
        catch (\Exception $e)
        {
            return new JsonResponse(array('status' => 'ERROR', 'message' => 'Při mazání widgetu došlo k chybě'), 500);
        }
    }


}
