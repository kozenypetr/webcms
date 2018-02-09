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
                'icon' => 'folder',
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
        $finder->depth('== 0')->notName('#.*thumbs.*#')->in($dir);

        $children  = [];
        $i = 0;
        foreach ($finder as $key => $item)
        {
            $children[$i]['text'] = $item->getFilename();
            $children[$i]['id']   = $relativeDir . '/' . $item->getRelativePathname();
            $children[$i]['children'] = false;
            $children[$i]['icon'] = ($item->getType() == 'dir')?'folder':$this->__getIconForFile($item->getFilename());
            // zjistime, jestli ma slozka nejak dalsi soubory
            if ($item->getType() == 'dir')
            {
                $childrenFinder->depth('0')->sortByName()->notName('#.*thumbs.*#')->in($dir . '/' . $item->getFilename());
                dump($item->getFilename());
                dump(count($childrenFinder));
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

    private function __getIconForFile($filename)
    {
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        return 'file' . (($extension)?" file-{$extension}":"");
    }

    /**
     * Seznam souboru v systemu ve stromu do postranniho panelu
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction()
    {
        $dir = realpath($this->get('kernel')->getRootDir() . '/../web/data');

        $finder = new Finder();
        $finder->directories()->depth('== 0')->notName('#.*thumbs.*#')->in($dir);

        return $this->render('CmsBundle:Backend/File:list.html.twig', array(
            'files' => $finder
        ));
    }
}
