<?php

namespace AdminBundle\Controller;

use CmsBundle\Entity\Gallery;
use CmsBundle\Entity\GalleryCategory;
use CmsBundle\Entity\Image;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Symfony\Component\HttpFoundation\JsonResponse;

use Intervention\Image\ImageManagerStatic as ImageEditor;

class GalleryAdminController extends Controller
{

    /**
     * @Route("/galeries/sortImages", name="gallery_admin_sort_image", options={"expose"=true})
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

                $qb = $em->getRepository('CmsBundle:Image')->createQueryBuilder('i');

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
     * @Route("/galeries/rotateImage/{id}", name="gallery_admin_rotate_image")
     */
    public function rotateImageAction(Request $request, Image $image)
    {
        $file = $this->getDataDir() . '/' . $image->getFilename();
        $im = ImageEditor::make($file);

        $im->rotate(90);
        $im->save($file);

        $json = array();
        $json['url'] = '/gallery/' . $image->getFilename() . '?t=' . time();
        $json['id']  = $image->getId();

        $response = new JsonResponse($json);

        return $response;
    }

    /**
     * @Route("/galeries/deleteImage/{id}", name="gallery_admin_delete_image")
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
     * @Route("/galeries/uploadImage/{id}", name="gallery_admin_upload_image")
     */
    public function uploadImageAction(Request $request, Gallery $gallery)
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
        $dql = "SELECT MAX(i.sort) AS max FROM CmsBundle\Entity\Image i WHERE i.gallery = ?1";

        $max = $em->createQuery($dql)
                  ->setParameter(1, $gallery->getId())
                  ->getSingleScalarResult();

        $image->setSort($max + 1);

        $image->setGallery($gallery);

        $file->move($this->getDataDir(), $image->getFilename());

        $im = ImageEditor::make($this->getDataDir() . '/' . $image->getFilename());
        $image->setWidth($im->width());
        $image->setHeight($im->height());
        $image->setFilesize(filesize($this->getDataDir() . '/' . $image->getFilename()));
        $image->setExtension($file->getClientOriginalExtension());

        $em->persist($image);
        $em->flush();

        return $this->render('AdminBundle:GalleryAdmin:images.html.twig', array('gallery' => $gallery));
    }

    /**
     * @Route("/galeries/imageList/{id}", name="gallery_admin_image_list")
     */
    public function imagesListAction(Gallery $gallery)
    {
        $em = $this->getDoctrine()->getManager();

        return $this->render('AdminBundle:GalleryAdmin:imagesList.html.twig', array('gallery' => $gallery));
    }


    protected function getDataDir()
    {
        return realpath($this->get('kernel')->getRootDir() . '/../web/gallery') . '/';
    }
}
