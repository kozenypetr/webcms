<?php

namespace AdminBundle\Controller;

use CmsBundle\Entity\Article\Item;
use CmsBundle\Entity\Article\Category;
use CmsBundle\Entity\Article\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\JsonResponse;

use Intervention\Image\ImageManagerStatic as ImageEditor;

class ArticleItemAdminController extends Controller
{

    /**
     * @Route("/article/item/sortImages", name="article_item_admin_sort_image", options={"expose"=true})
     */
    public function sortImagesAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $sort = $request->get('sort');

        $json = array();
        $json['status'] = 'OK';

        try {
            foreach ($sort as $key => $image) {
                $galleryId = preg_replace('|[^0-9]|', '', $image);

                $qb = $em->getRepository('CmsBundle:Article\Image')->createQueryBuilder('i');

                $q = $qb->update()
                        ->set('i.sort', '?1')
                        ->where('i.id = ?2')
                        ->setParameter(1, $key)
                        ->setParameter(2, $galleryId)
                        ->getQuery();

                $p = $q->execute();
            }

        }
        catch (\Exception $e)
        {
            $json['status'] = 'ERROR';
            $json['message'] = $e->getMessage();
        }

        $response = new JsonResponse($json);

        return $response;
    }


    /**
     * @Route("/article/item/rotateImage/{id}", name="article_item_admin_rotate_image")
     */
    public function rotateImageAction(Request $request, Image $image)
    {
        $file = $this->getDataDir() . '/' . $image->getFilename();
        $im = ImageEditor::make($file);

        $im->rotate(90);
        $im->save($file);

        $json = array();
        $json['url'] = '/article/' . $image->getFilename() . '?t=' . time();
        $json['id']  = $image->getId();

        $response = new JsonResponse($json);

        return $response;
    }

    /**
     * @Route("/article/item/deleteImage/{id}", name="article_item_admin_delete_image")
     */
    public function deleteImageAction(Request $request, Image $image)
    {
        $json = array();
        $json['id']  = $image->getId();

        $em = $this->getDoctrine()->getManager();
        $em->remove($image);
        $em->flush();

        $response = new JsonResponse($json);

        return $response;
    }


    /**
     * @Route("/article/item/uploadImage/{id}", name="article_item_admin_upload_image")
     */
    public function uploadImageAction(Request $request, Item $item)
    {
        $em = $this->getDoctrine()->getManager();
        /**
         * @var UploadedFile $file
         */
        $file = $request->files->get('file');

        $image = new Image();

        $extension = $file->getClientOriginalExtension();
        $originFilename = $file->getClientOriginalName();

        // $image->setExtension($extension);
        $image->setOriginFilename($originFilename);
        $image->setFilename(md5(date('dmYHis') . $originFilename) . '.' . $extension);

        // ziskame maximalni razeni
        $dql = "SELECT MAX(i.sort) AS max FROM CmsBundle\Entity\Article\Image i WHERE i.item = ?1";

        $max = $em->createQuery($dql)
                  ->setParameter(1, $item->getId())
                  ->getSingleScalarResult();

        $image->setSort($max + 1);

        $image->setItem($item);

        $file->move($this->getDataDir(), $image->getFilename());

        $im = ImageEditor::make($this->getDataDir() . '/' . $image->getFilename());
        $image->setWidth($im->width());
        $image->setHeight($im->height());
        $image->setFilesize(filesize($this->getDataDir() . '/' . $image->getFilename()));
        $image->setExtension($file->getClientOriginalExtension());

        $em->persist($image);
        $em->flush();

        return $this->render('AdminBundle:Article/ItemAdmin:images.html.twig', array('item' => $item));
    }

    /**
     * @Route("/article/item/imageList/{id}", name="article_item_admin_image_list")
     */
    public function imagesListAction(Item $item)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AdminBundle:Article/ItemAdmin:imagesList.html.twig', array('item' => $item));
    }


    protected function getDataDir()
    {
        return realpath($this->get('kernel')->getRootDir() . '/../web/article') . '/';
    }
}
