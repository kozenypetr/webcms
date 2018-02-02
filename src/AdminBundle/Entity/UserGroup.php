<?php
// src/AppBundle/Entity/User.php

namespace AdminBundle\Entity;

// use FOS\UserBundle\Model\User as BaseUser;
use Sonata\UserBundle\Entity\BaseGroup;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user_group")
 */
class UserGroup extends BaseGroup
{
  /**
   * @ORM\Id
   * @ORM\Column(type="integer")
   * @ORM\GeneratedValue(strategy="AUTO")
   */
  protected $id;


  /**
   * Get id
   *
   * @return integer $id
   */
  public function getId()
  {
    return $this->id;
  }
}
