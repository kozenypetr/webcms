<?php

namespace CmsBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Imagine\Image\ImageInterface;


/**
 * Image
 * @ORM\Table(name="widget_image")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\WidgetImageRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class WidgetImage
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
     * @var int
     *
     * @ORM\Column(name="sort", type="integer")
     */
    private $sort;

    /**
     * @ORM\ManyToOne(targetEntity="Widget", inversedBy="images")
     * @ORM\JoinColumn(name="widget_id", referencedColumnName="id")
     */
    private $widget;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255)
     */
    private $filename;

    /**
     * @var string
     *
     * @ORM\Column(name="extension", type="string", length=255)
     */
    private $extension;

    /**
     * @var string
     *
     * @ORM\Column(name="alt", type="string", length=255, nullable=true)
     */
    private $alt;

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
     * Set sort.
     *
     * @param int $sort
     *
     * @return WidgetImage
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
     * Set widget.
     *
     * @param \CmsBundle\Entity\Widget|null $widget
     *
     * @return WidgetImage
     */
    public function setWidget(\CmsBundle\Entity\Widget $widget = null)
    {
        $this->widget = $widget;

        return $this;
    }

    /**
     * Get widget.
     *
     * @return \CmsBundle\Entity\Widget|null
     */
    public function getWidget()
    {
        return $this->widget;
    }

    /**
     * Set filename.
     *
     * @param string $filename
     *
     * @return WidgetImage
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
     * Set extension.
     *
     * @param string $extension
     *
     * @return WidgetImage
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

    public function getSrc()
    {
        return '/images/widget/' . $this->getWidget()->getId() . '/' . $this->getFilename();
    }

    /**
     * Set alt.
     *
     * @param string|null $alt
     *
     * @return WidgetImage
     */
    public function setAlt($alt = null)
    {
        $this->alt = $alt;

        return $this;
    }

    /**
     * Get alt.
     *
     * @return string|null
     */
    public function getAlt()
    {
        return $this->alt;
    }
}
