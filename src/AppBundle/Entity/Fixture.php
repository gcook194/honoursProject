<?php

//Class to hold data about win / draw points, number of fixtures, table sort order etc
namespace AppBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\MaxDepth;

/**
 * @ORM\Entity
 * @ORM\Table(name="fixture")
 */
class Fixture {
    
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="League", inversedBy="fixtures")
    * @ORM\JoinColumn(name="league_id", referencedColumnName="id")
    * @Assert\NotBlank(message="A fixture must belong to a league")
     * @MaxDepth(2)
     */
    private $league;
    
    /**
    * @ORM\ManyToOne(targetEntity="Team")
    * @ORM\JoinColumn(name="home_team_id", referencedColumnName="id")
     * @MaxDepth(2)
    */
    private $homeTeam;
    
    /**
    * @ORM\ManyToOne(targetEntity="Team")
    * @ORM\JoinColumn(name="away_team_id", referencedColumnName="id")
     * @MaxDepth(2)
    */
    private $awayTeam;
    
    /**
    * @ORM\Column(name="home_goals", type="integer", nullable=true)
    */
    private $homeGoals;

    /**
    * @ORM\Column(name="away_goals", type="integer", nullable=true)
    */
    private $awayGoals;

    /**
    * @ORM\Column(name="scheduled_date", type="date", nullable=true)
    */
    private $scheduledDate;

    /**
    * @ORM\Column(name="played_date", type="date", nullable=true)
    */
    private $playedDate;

    /**
    * @ORM\Column(name="postponed", type="boolean", nullable=true)
    */
    private $postponed;

    /**
    * @ORM\Column(name="abandoned", type="boolean", nullable=true)
    */
    private $abandoned;

    /**
     * @ORM\Column(name="fixture_round", type="integer")
     */
    private $round;

    /**
     * @ORM\Column(name="fixture_played", type="boolean")
     */
    private $played;

    /**
     * @ORM\OneToMany(targetEntity="FixtureEvent", mappedBy="fixture", fetch="EAGER")
     * @ORM\OrderBy({"time" = "ASC", "eventType" = "ASC"})
     * @MaxDepth(2)
     */
    private $events;

    public function __construct()
    {
        $this->scheduledDate = new \DateTime();
        $this -> events = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(){
		return $this->id;
	}

	public function setId($id){
		$this->id = $id;
	}

	public function getLeague(){
		return $this->league;
	}

	public function setLeague($league){
		$this->league = $league;
	}

	public function getHomeTeam(){
		return $this->homeTeam;
	}

	public function setHomeTeam($homeTeam){
		$this->homeTeam = $homeTeam;
	}

	public function getAwayTeam(){
		return $this->awayTeam;
	}

	public function setAwayTeam($awayTeam){
		$this->awayTeam = $awayTeam;
	}

	public function getHomeGoals(){
		return $this->homeGoals;
	}

	public function setHomeGoals($homeGoals){
		$this->homeGoals = $homeGoals;
	}

	public function getAwayGoals(){
		return $this->awayGoals;
	}

	public function setAwayGoals($awayGoals){
		$this->awayGoals = $awayGoals;
	}

	public function getScheduledDate(){
		return $this->scheduledDate;
	}

	public function setScheduledDate($scheduledDate){
		$this->scheduledDate = $scheduledDate;
	}

	public function getPlayedDate(){
		return $this->playedDate;
	}

	public function setPlayedDate($playedDate){
		$this->playedDate = $playedDate;
	}

	public function getPostponed(){
		return $this->postponed;
	}

	public function setPostponed($postponed){
		$this->postponed = $postponed;
	}

	public function getAbandoned(){
		return $this->abandoned;
	}

	public function setAbandoned($abandoned){
		$this->abandoned = $abandoned;
	}

    public function getRound() {
        return $this->round;
    }

    public function setRound($round) {
        $this->round = $round;
    }

    public function getPlayed() {
        return $this->played;
    }

    public function setPlayed($played) {
        $this->played = $played;
    }

    public function getEvents() {
        return $this->events;
    }

    public function setEvents($events) {
        $this->events = $events;
    }
}