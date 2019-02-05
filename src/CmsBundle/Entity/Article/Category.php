<?php

namespace CmsBundle\Entity\Article;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\File\UploadedFile;


use CmsBundle\Entity\Article\Item;
use CmsBundle\Entity\Article\Image;
use CmsBundle\Entity\Article\Tag;

use Gedmo\Mapping\Annotation as Gedmo;
use Gedmo\Timestampable\Traits\TimestampableEntity;


/**
 * GalleryCategory
 *
 * @ORM\Table(name="article_category")
 * @ORM\Entity(repositoryClass="CmsBundle\Repository\Article\CategoryRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class Category
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
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="locale", type="string", length=2)
     */
    private $locale = "cs";

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="subname", type="string", length=255, nullable=true)
     */
    private $subname;

    /**
     * @var string
     *
     * @ORM\Column(name="annotation", type="text", nullable=true)
     */
    private $annotation;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="text", type="text", nullable=true)
     */
    private $text;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="string", length=255, nullable=true)
     */
    private $filename;

    /**
     * Unmapped property to handle file uploads
     */
    private $file;

    /**
     * @var string
     *
     * @ORM\Column(name="icon", type="string", length=255, nullable=true)
     */
    private $icon;

    /**
     * @var int
     *
     * @ORM\Column(name="sort", type="integer", nullable=true)
     */
    private $sort;

    /**
     * @var string
     *
     *
     * @ORM\Column(name="slug", type="string", length=255)
     * @Gedmo\Slug(fields={"name"})
     */
    private $slug;

    /**
     * @var int
     *
     * @ORM\Column(name="parent_url", type="string", length=255, nullable=true)
     */
    private $parentUrl;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display_breadcrumb", type="boolean", options={"default": true})
     */
    private $displayBreadcrumb;

    /**
     * @var boolean
     *
     * @ORM\Column(name="display_parent_breadcrumb", type="boolean", options={"default": true})
     */
    private $displayParentBreadcrumb;

    /**
     * One Product has Many Images.
     * @ORM\OneToMany(targetEntity="Item", mappedBy="category")
     */
    private $items;

    /**
     * @var string
     *
     *
     * @ORM\Column(name="template_list", type="string", length=255, nullable=true)
     */
    private $templateList;


    /**
     * @var string
     *
     *
     * @ORM\Column(name="template_detail", type="string", length=255, nullable=true)
     */
    private $templateDetail;


    /**
     * @var string
     *
     * @ORM\Column(name="custom_metatitle", type="string", length=255, nullable=true)
     */
    private $customMetatitle;


    /**
     * @var string
     *
     * @ORM\Column(name="custom_metadescription", type="text", nullable=true, nullable=true)
     */
    private $customMetadescription;


    /**
     * @var string
     *
     * @ORM\Column(name="custom_metakeywords", type="string", length=255, nullable=true)
     */
    private $customMetakeywords;


    public function __toString() {
        return sprintf("%s [%s]", (string)$this->getName(), $this->getLocale());
    }


    /**
     * Manages the copying of the file to the relevant place on the server
     */
    public function upload()
    {
        // the file property can be empty if the field is not required
        if (null === $this->getFile()) {
            return;
        }

        // we use the original file name here but you should
        // sanitize it at least to avoid any security issues
        // move takes the target directory and target filename as params
        $this->getFile()->move(
            $this->getUploadDir(),
            $this->getFile()->getClientOriginalName()
        );

        // set the path property to the filename where you've saved the file
        $this->filename = '/data/article/' . $this->getFile()->getClientOriginalName();

        // clean up the file property as you won't need it anymore
        $this->setFile(null);
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getUploadDir()
    {
        return realpath(dirname(__FILE__) . '/../../../web/data/article');
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function lifecycleFileUpload()
    {
        $this->upload();
    }


    /**
     * Updates the hash value to force the preUpdate and postUpdate events to fire
     */
    public function refreshUpdated()
    {
        $this->setUpdatedAt(new \DateTime());
    }


    public function getMetatitle()
    {
        return $this->getCustomMetatitle()?$this->getCustomMetatitle():$this->getName();
    }

    public function getMetadescription()
    {
        return $this->getCustomMetadescription()?$this->getCustomMetadescription():'';
    }

    public function getMetakeywords()
    {
        return $this->getCustomMetakeywords()?$this->getCustomMetakeywords():'';
    }

    public function getUrl()
    {
        $path = '';
        if ($this->getParentUrl())
        {
            $path = $this->getCategory()->getParentUrl() . '/';
        }
        return  $path . $this->getSlug();
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
     * @return Category
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
     * Set locale.
     *
     * @param string $locale
     *
     * @return Category
     */
    public function setLocale($locale)
    {
        $this->locale = $locale;

        return $this;
    }

    /**
     * Get locale.
     *
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Category
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set filename.
     *
     * @param string|null $filename
     *
     * @return Category
     */
    public function setFilename($filename = null)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set icon.
     *
     * @param string|null $icon
     *
     * @return Category
     */
    public function setIcon($icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set sort.
     *
     * @param int|null $sort
     *
     * @return Category
     */
    public function setSort($sort = null)
    {
        $this->sort = $sort;

        return $this;
    }

    /**
     * Get sort.
     *
     * @return int|null
     */
    public function getSort()
    {
        return $this->sort;
    }

    /**
     * Set slug.
     *
     * @param string $slug
     *
     * @return Category
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->items = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add item.
     *
     * @param \CmsBundle\Entity\Article\Item $item
     *
     * @return Category
     */
    public function addItem(\CmsBundle\Entity\Article\Item $item)
    {
        $this->items[] = $item;

        return $this;
    }

    /**
     * Remove item.
     *
     * @param \CmsBundle\Entity\Article\Item $item
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeItem(\CmsBundle\Entity\Article\Item $item)
    {
        return $this->items->removeElement($item);
    }

    /**
     * Get items.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set subname.
     *
     * @param string|null $subname
     *
     * @return Category
     */
    public function setSubname($subname = null)
    {
        $this->subname = $subname;

        return $this;
    }

    /**
     * Get subname.
     *
     * @return string|null
     */
    public function getSubname()
    {
        return $this->subname;
    }

    /**
     * Set annotation.
     *
     * @param string|null $annotation
     *
     * @return Category
     */
    public function setAnnotation($annotation = null)
    {
        $this->annotation = $annotation;

        return $this;
    }

    /**
     * Get annotation.
     *
     * @return string|null
     */
    public function getAnnotation()
    {
        return $this->annotation;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Category
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set text.
     *
     * @param string|null $text
     *
     * @return Category
     */
    public function setText($text = null)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text.
     *
     * @return string|null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set templateList.
     *
     * @param string|null $templateList
     *
     * @return Category
     */
    public function setTemplateList($templateList = null)
    {
        $this->templateList = $templateList;

        return $this;
    }

    /**
     * Get templateList.
     *
     * @return string|null
     */
    public function getTemplateList()
    {
        return $this->templateList;
    }

    /**
     * Set templateDetail.
     *
     * @param string|null $templateDetail
     *
     * @return Category
     */
    public function setTemplateDetail($templateDetail = null)
    {
        $this->templateDetail = $templateDetail;

        return $this;
    }

    /**
     * Get templateDetail.
     *
     * @return string|null
     */
    public function getTemplateDetail()
    {
        return $this->templateDetail;
    }

    /**
     * Set customMetatitle.
     *
     * @param string|null $customMetatitle
     *
     * @return Category
     */
    public function setCustomMetatitle($customMetatitle = null)
    {
        $this->customMetatitle = $customMetatitle;

        return $this;
    }

    /**
     * Get customMetatitle.
     *
     * @return string|null
     */
    public function getCustomMetatitle()
    {
        return $this->customMetatitle;
    }

    /**
     * Set customMetadescription.
     *
     * @param string|null $customMetadescription
     *
     * @return Category
     */
    public function setCustomMetadescription($customMetadescription = null)
    {
        $this->customMetadescription = $customMetadescription;

        return $this;
    }

    /**
     * Get customMetadescription.
     *
     * @return string|null
     */
    public function getCustomMetadescription()
    {
        return $this->customMetadescription;
    }

    /**
     * Set customMetakeywords.
     *
     * @param string|null $customMetakeywords
     *
     * @return Category
     */
    public function setCustomMetakeywords($customMetakeywords = null)
    {
        $this->customMetakeywords = $customMetakeywords;

        return $this;
    }

    /**
     * Get customMetakeywords.
     *
     * @return string|null
     */
    public function getCustomMetakeywords()
    {
        return $this->customMetakeywords;
    }

    /**
     * Set parentUrl.
     *
     * @param string|null $parentUrl
     *
     * @return Category
     */
    public function setParentUrl($parentUrl = null)
    {
        $this->parentUrl = $parentUrl;

        return $this;
    }

    /**
     * Get parentUrl.
     *
     * @return string|null
     */
    public function getParentUrl()
    {
        return $this->parentUrl;
    }

    /**
     * Set displayParentBreadcrumb.
     *
     * @param bool $displayParentBreadcrumb
     *
     * @return Category
     */
    public function setDisplayParentBreadcrumb($displayParentBreadcrumb)
    {
        $this->displayParentBreadcrumb = $displayParentBreadcrumb;

        return $this;
    }

    /**
     * Get displayParentBreadcrumb.
     *
     * @return bool
     */
    public function getDisplayParentBreadcrumb()
    {
        return $this->displayParentBreadcrumb;
    }

    /**
     * Set displayBreadcrumb.
     *
     * @param bool $displayBreadcrumb
     *
     * @return Category
     */
    public function setDisplayBreadcrumb($displayBreadcrumb)
    {
        $this->displayBreadcrumb = $displayBreadcrumb;

        return $this;
    }

    /**
     * Get displayBreadcrumb.
     *
     * @return bool
     */
    public function getDisplayBreadcrumb()
    {
        return $this->displayBreadcrumb;
    }
}
