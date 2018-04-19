<?php

namespace CmsBundle\Controller\Editor;



use CmsBundle\Form\WidgetType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


use Symfony\Component\HttpFoundation\JsonResponse;

use CmsBundle\Entity\Document;
use CmsBundle\Entity\DocumentCategory;

use CmsBundle\Repository\DocumentRepository;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;

use Symfony\Component\Finder\Finder;

/**
 * @Route("/cms/file")
 */
class FileController extends Controller
{
    /**
     * Nacitani stromu souboru
     * @Route("/node", name="cms_file_node", options={"expose"=true})
     * @Method({"GET"})
     */
    public function nodeAction(Request $request)
    {
        $id = $request->get('id');

        $basedir = realpath($this->get('kernel')->getRootDir() . '/../web/data');

        $node = [];
        if ($id == '#')
        {
            $dir = $basedir;

            $node[0] = ['text' => 'Soubory',
                'id' => '/',
                'icon' => 'fa fa-folder',
                'state' => ['opened' => true]
            ];

            $node[0]['children'] = [];
            $relativeDir = '';
        }
        else
        {
            $dir = $basedir . '/' . $id;
            $relativeDir = $id;
        }

        $finder = new Finder();
        $childrenFinder = new Finder();
        $finder->depth('== 0')->sortByName()->notName('#.*thumbs.*#')->in($dir);

        $children  = [];
        $i = 0;
        foreach ($finder as $key => $item)
        {
            $children[$i]['text'] = $item->getFilename();
            $children[$i]['id']   = $relativeDir . '/' . $item->getRelativePathname();
            $children[$i]['children'] = false;
            $children[$i]['icon'] = ($item->getType() == 'dir')?'fa fa-folder':$this->__getIconForFile($item->getFilename());
            // zjistime, jestli ma slozka nejak dalsi soubory
            if ($item->getType() == 'dir')
            {
                $childrenFinder->depth('0')->sortByName()->notName('#.*thumbs.*#')->in($dir . '/' . $item->getFilename());
                // dump($item->getFilename());
                // dump(count($childrenFinder));
                if (count($childrenFinder) > 0)
                {
                    $children[$i]['children'] = true;
                }
            }
            $i++;
        }

        if ($id == '#')
        {
            $node[0]['children'] = $children;
        }
        else
        {
            $node = $children;
        }

        return new JsonResponse($node);
    }


    /**
     * Nacitani stromu souboru
     * @Route("/image/preview", name="cms_image_preview", options={"expose"=true})
     * @Method({"GET"})
     */
    public function imagePreviewAction(Request $request)
    {
        $file = $request->get('file');

        $file = str_replace('_anchor', '', $file);

        $extension = pathinfo($file, PATHINFO_EXTENSION);

        if (!in_array($extension, array('png', 'jpg', 'gif')))
        {
            return new JsonResponse(array('status' => 'noimage'));
        }

        $imagineCacheManager = $this->get('liip_imagine.cache.manager');

        /** @var string */
        $resolvedPath = $imagineCacheManager->getBrowserPath('/data/' . $file, 'cms_image_thumb');

        return new JsonResponse(['status' => 'image', 'path' => $resolvedPath]);
    }


    /**
     * @param $filename
     *
     * @return string
     */
    private function __getIconForFile($filename)
    {
        $files = array();
        $files['jpg']  = 'fa-file-image-o';
        $files['jpeg'] = 'fa-file-image-o';
        $files['png']  = 'fa-file-image-o';
        $files['gif']  = 'fa-file-image-o';
        $files['bmp']  = 'fa-file-image-o';

        $files['pdf']   = 'fa-file-pdf-o';
        $files['xls']   = 'fa-file-excel-o';
        $files['xlsx']  = 'fa-file-excel-o';
        $files['doc']   = 'fa-file-word-o';
        $files['docx']  = 'fa-file-word-o';

        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        return 'fa ' . ((isset($files[$extension]))?$files[$extension]:"fa-file-o");
    }

}
