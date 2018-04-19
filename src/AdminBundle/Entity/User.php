<?php
// src/AppBundle/Entity/User.php

namespace AdminBundle\Entity;

// use FOS\UserBundle\Model\User as BaseUser;
use Sonata\UserBundle\Entity\BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Sonata\UserBundle\Model\UserInterface;


/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;

  /**
   * @ORM\ManyToMany(targetEntity="AdminBundle\Entity\UserGroup")
   * @ORM\JoinTable(name="fos_user_user_group",
   *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
   *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
   * )
   */
  protected $groups;



  public function __construct()
  {
    parent::__construct();
    // your own logic
  }

  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId()
  {
    return $this->id;
  }

    /**
     * Returns the gender list.
     *
     * @return array
     */
    public static function getGenderList()
    {
        return [
            UserInterface::GENDER_UNKNOWN => 'gender_unknown',
            UserInterface::GENDER_FEMALE  => 'gender_female',
            UserInterface::GENDER_MALE => 'gender_male'
        ];
    }
}
