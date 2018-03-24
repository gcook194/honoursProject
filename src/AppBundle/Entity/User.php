<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Entity\League;

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
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Please enter your first name", groups={"Registration", "Profile"})
     */
    protected $first_name; 
    
    /**
     * @ORM\Column(type="string", length=100)
     * @Assert\NotBlank(message="Please enter your last namw", groups={"Registration", "Profile"})
     */
    protected $last_name;
    
    /**
    * @ORM\Column(type="string")
    * @Assert\NotBlank(message="You must choose a user type", groups={"Registration", "Profile"})
    *
    */
    protected $user_type;

    /**
     * Many Users have Many Groups.
     * @ORM\ManyToMany(targetEntity="League", inversedBy="users")
     * @ORM\JoinTable(name="user_leagues")
     */
    protected $leagues;

    public function __construct()
    {
        parent::__construct();
        // your own logic

        $this->leagues = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    public function getFirstName()
    {
        return $this->first_name;
    }
    
    public function setFirstName($first_name)
    {
        $this->first_name = $first_name;
    }
    
    public function getLastName()
    {
        return $this->last_name;
    }
    
    public function setLastName($last_name)
    {
        $this->last_name = $last_name;
    }
    
    public function setUserType($user_type)
    {
        $this->user_type = $user_type;
    }
    
    public function getUserType() 
    {
        return $this->user_type;
    }

    public function getLeagues()
    {
        return $this->leagues;
    }

    public function setLeagues($leagues)
    {
        $this->leagues = $leagues;
    }

    public function addLeague(League $league)
    {
        if ($this->leagues->contains($league)) {
            return;
        }

        $this->leagues[] = $league;
    }
}
