<?php

namespace AppBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Appbundle\Entity\LeagueMeta;
use Appbundle\Entity\Team;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeagueRepository")
 * @ORM\Table(name="league")
 */
class League {
    
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id; 
    
    /**
    * @ORM\Column(type="string", length=100)
    * @Assert\NotBlank(message="Please name your league")
    */
    private $name; 

    /**
    * @ORM\OneToOne(targetEntity="LeagueMeta", cascade={"persist"})
    * @ORM\JoinColumn(name="league_meta_id", referencedColumnName="id")
    */
    private $leagueMeta;

    /**
     * One league has  one administrator.
     * @ORM\OneToOne(targetEntity="User")
     * @ORM\JoinColumn(name="administrator_id", referencedColumnName="id")
     */
    private $administrator;

    /**
     * One Product has Many Features.
     * @ORM\OneToMany(targetEntity="Fixture", mappedBy="league")
     */
    private $fixtures;

    /**
     * @ORM\OneToMany(targetEntity="Team", mappedBy="league", fetch="EAGER")
     */
    private $teams;

    /**
     * Many Groups have Many Users.
     * @ORM\ManyToMany(targetEntity="User", mappedBy="leagues")
     */
    private $users;
    
    function __construct() {
        $this -> fixtures = new \Doctrine\Common\Collections\ArrayCollection();
        $this -> teams = new \Doctrine\Common\Collections\ArrayCollection();
        $this -> users = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
	public function getId() {
		return $this->id;
	}

	public function setId($id) { 
		$this->id = $id;
	}

	public function getName() {
		return $this->name;
	}

	public function setName($name) {
		$this->name = $name;
	}

	public function getLeagueMeta() {
		return $this->leagueMeta;
	}

	public function setLeagueMeta($leagueMeta) {
		$this->leagueMeta = $leagueMeta;
	}

    public function getAdministrator() {
        return $this->administrator;
    }

    public function setAdministrator($administrator) {
        $this->administrator = $administrator;
    }

    public function getFixtures()
    {
        return $this->fixtures;
    }

    public function setFixtures($fixtures)
    {
        $this->fixtures = $fixtures;
    }

    public function getTeams()
    {
        return $this->teams;
    }

    public function setTeams($teams)
    {
        $this->teams = $teams;
    }

    public function getUsers()
    {
        return $this->users;
    }

    public function setUsers($users)
    {
        $this->users = $users;
    }

    public function addUser(User $user)
    {
        if ($this->users->contains($user)) {
            return;
        }

        $this->users[] = $user;
    }
}