<?php

namespace CmsBundle\Controller\Editor;


use CmsBundle\Entity\Document;
use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Widget;
use CmsBundle\Entity\Box;
use CmsBundle\Entity\WidgetImage;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;


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
        $html = $service->setEntity($widget)->getHtml();

        // vracime JSON - nove ID + HTML
        return new JsonResponse(array('id' => $widget->getId(), 'widgetHtml' => $html));
    }

    /**
     * Kopie widgetu
     * @Route("/copy/{id}", name="cms_widget_copy", options={"expose"=true})
     * @Method({"POST"})
     */
    public function copyAction(Request $request, Widget $copyWidget)
    {
        $em = $this->getDoctrine()->getManager();

        // parametry vlozeni - region, typ region [page/global], dokument, prev, next
        $parameters = json_decode($request->get('parameters'), true);

        // vytvorime objekt widgetu
        $widget = $this->getDoctrine()
            ->getRepository(Widget::class)
            ->copyWidget($parameters, $copyWidget);

        // vychozi nastaveni widgetu
        // $widget->setParameters($this->get($widget->getService())->getDefault());

        // ulozime widget
        $em->persist($widget);
        $em->flush();

        $service = $this->get($widget->getService());

        // ziskame HTML widgetu
        $html = $service->setEntity($widget)->getHtml();

        // vracime JSON - nove ID + HTML
        return new JsonResponse(array('id' => $widget->getId(), 'widgetHtml' => $html));
    }


    /**
     * Pridani obrazku k widgetu
     * @Route("/addImage/{id}", name="cms_widget_add_image", options={"expose"=true})
     * @Method({"PUT"})
     */
    public function addImageAction(Request $request, Widget $widget)
    {
        $em = $this->getDoctrine()->getManager();

        $file = $request->get('file');

        $file = str_replace('_anchor', '', $file);

        $sourceDir = realpath($this->get('kernel')->getRootDir() . '/../web/data');

        $basedir = realpath($this->get('kernel')->getRootDir() . '/../web/images');

        $destDir = $basedir . DIRECTORY_SEPARATOR . 'widget' . DIRECTORY_SEPARATOR . $widget->getId();

        $fs = new Filesystem();

        if (!is_dir($destDir))
        {
            $fs->mkdir($destDir, 0777);
        }

        $source = $sourceDir .  DIRECTORY_SEPARATOR . $file;

        $q = $em->createQuery('DELETE FROM CmsBundle:WidgetImage im WHERE im.widget = ?1')
                ->setParameter(1, $widget->getId());

        $q->execute();

        if (is_dir($source))
        {

        }
        elseif (file_exists($source))
        {
            $destFilename  = pathinfo($file, PATHINFO_BASENAME);
            $destExtension = pathinfo($file, PATHINFO_EXTENSION);

            try
            {
                $fs->copy($source, $destDir . DIRECTORY_SEPARATOR .  $destFilename, true);
            }
            catch (\Exception $e)
            {
                return new JsonResponse(array('status' => 'ERROR', 'message' => $e->getMessage(), 'id' => $widget->getId(), 'file' => $file));
            }

            $image = new WidgetImage();
            $image->setFilename($destFilename);
            $image->setExtension($destExtension);
            $image->setWidget($widget);
            $image->setSort(1);

            $em->persist($image);
            $em->flush();
        }

        return new JsonResponse(array('status' => 'SUCCESS', 'id' => $widget->getId(), 'file' => $file));
    }


    /**
     * Nacteni obsahu widgetu
     * @Route("/reload/{id}", name="cms_widget_reload", options={"expose"=true})
     * @Method({"GET"})
     */
    public function reloadAction(Request $request, Widget $widget)
    {
        $service = $this->get($widget->getService());

        $html = $service->setEntity($widget)->getHtml(true);

        return new JsonResponse(array('id' => $widget->getId(), 'html' => $html, 'class' => $widget->getClass()));
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
     * Seznam widgetu k pridani
     * @Route("/list", name="cms_widget_list")
     * @Method({"GET"})
     */
    public function listAction(Request $request)
    {
        return $this->render('CmsBundle:Editor/Toolbar:toolbar.widgetslist.html.twig', array(
            'widgets' => $this->get('cms.manager.widget')->getWidgets()
        ));
    }


    /**
     * Editace widgetu
     * @Route("/edit/{id}", name="cms_widget_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Widget $widget)
    {
        $service = $this->get($widget->getService());

        $parameters = $widget->getParameters();

        $parameters['is_system'] = $widget->getIsSystem();

        $form = $service->setParameters($parameters)
                        ->createForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $widget->setIsSystem($data['is_system']);
            $widget->setParameters($service->processParameters($data));

            $em = $this->getDoctrine()->getManager();
            $em->persist($widget);
            $em->flush();

            // $html = $this->get('templating')->render($service->getTemplate(), array('widget' => $widget, 'title' => $service->getTitle(), 'ajax' => true));
            $html = $service->setEntity($widget)->getHtml(true);

            return new JsonResponse(array('html' => $html, 'class' => $widget->getClass()));
        }

        $statusCode = 200;

        if ($form->isSubmitted() && !$form->isValid()){
            $statusCode = 400;
            dump($form->getErrors(true, false));
        }

        return $this->render('CmsBundle:Editor/Form:' . $service->getEditorTemplate(), array(
            'widget' => $widget,
            'defaultTemplate' => $service->getTemplate(),
            'form' => $form->createView(),
            'parameters' => $service->setEntity($widget)->getWidgetParameters(true)
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
