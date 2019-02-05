<?php

namespace CmsBundle\Entity\Article;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Sonata\TranslationBundle\Model\Gedmo\AbstractPersonalTranslatable;
use Sonata\TranslationBundle\Model\Gedmo\TranslatableInterface;

use CmsBundle\Entity\Article\Item;
use CmsBundle\Entity\Article\Category;
use CmsBundle\Entity\Article\Tag;


/**
 * Image
 *
 * @ORM\Table(name="article_image")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\Article\ImageRepository")
 */
class Image
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", options={"default": true})
     */
    private $isActive = true;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="origin_filename", type="string", length=255)
     */
    private $originFilename;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @var int
     *
     * @ORM\Column(name="filesize", type="integer")
     */
    private $filesize;

    /**
     * @var int
     *
     * @ORM\Column(name="width", type="integer")
     */
    private $width;

    /**
     * @var int
     *
     * @ORM\Column(name="height", type="integer")
     */
    private $height;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=8)
     */
    private $extension;


    /**
     * Many Images have One Item.
     * @ORM\ManyToOne(targetEntity="Item", inversedBy="images", cascade={"persist"})
     * @ORM\JoinColumn(name="item_id", referencedColumnName="id")
     */
    private $item;


    public function getPath()
    {
        return '/article/' . $this->getFilename();
    }


    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set isActive.
     *
     * @param bool $isActive
     *
     * @return Image
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive.
     *
     * @return bool
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return Image
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set originFilename.
     *
     * @param string $originFilename
     *
     * @return Image
     */
    public function setOriginFilename($originFilename)
    {
        $this->originFilename = $originFilename;

        return $this;
    }

    /**
     * Get originFilename.
     *
     * @return string
     */
    public function getOriginFilename()
    {
        return $this->originFilename;
    }

    /**
     * Set sort.
     *
     * @param int $sort
     *
     * @return Image
     */
    public function setSort($sort)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set filesize.
     *
     * @param int $filesize
     *
     * @return Image
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize.
     *
     * @return int
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set width.
     *
     * @param int $width
     *
     * @return Image
     */
    public function setWidth($width)
    {
        $this->width = $width;

        return $this;
    }

    /**
     * Get width.
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Set height.
     *
     * @param int $height
     *
     * @return Image
     */
    public function setHeight($height)
    {
        $this->height = $height;

        return $this;
    }

    /**
     * Get height.
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Set extension.
     *
     * @param string $extension
     *
     * @return Image
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set item.
     *
     * @param \CmsBundle\Entity\Article\Item|null $item
     *
     * @return Image
     */
    public function setItem(\CmsBundle\Entity\Article\Item $item = null)
    {
        $this->item = $item;

        return $this;
    }

    /**
     * Get item.
     *
     * @return \CmsBundle\Entity\Article\Item|null
     */
    public function getItem()
    {
        return $this->item;
    }
}
