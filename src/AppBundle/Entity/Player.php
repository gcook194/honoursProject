<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/02/2018
 * Time: 14:37
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="Player")
 */
class Player
{

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\Column(name="first_name", type="string")
     * @Assert\NotBlank)
     */
    private $firstName;

    /**
     * @ORM\Column(name="last_name", type="string")
     * @Assert\NotBlank)
     */
    private $lastName;

    /**
     * @ORM\Column(name="position", type="string")
     * @Assert\NotBlank)
     */
    private $position;

    /**
     * @ORM\Column(name="number", type="integer")
     * @Assert\NotBlank)
     * @Assert\Range(
     *      min = 1,
     *      max = 99,
     *      minMessage = "{{ limit }} is the lowest number you can enter",
     *      maxMessage = "{{ limit }} is the highest number you can enter"
     * )
     */
    private $number;

    /**
    * @ORM\Column(name="games_played", type="integer")
    */
    private $gamesPlayed;

    /**
     * @ORM\Column(name="goals_scored", type="integer")
     */
    private $goalsScored;

    /**
     * @ORM\Column(name="clean_sheets", type="integer")
     */
    private $cleanSheets;

    /**
     * @ORM\Column(name="goals_assisted", type="integer")
     */
    private $assists;

    /**
     * Many Players have One Team.
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="players")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id")
     */
    private $team;

    public function __construct()
    {
        //set up default statistics when generating entity
        $this->gamesPlayed = 0;
        $this->goalsScored = 0;
        $this->assists = 0;
        $this->cleanSheets = 0;
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFirstName()
    {
        return $this->firstName;
    }

    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    public function getLastName()
    {
        return $this->lastName;
    }

    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition($position)
    {
        $this->position = $position;
    }

    public function getNumber()
    {
        return $this->number;
    }

    public function setNumber($number)
    {
        $this->number = $number;
    }

    public function getGamesPlayed()
    {
        return $this->gamesPlayed;
    }

    public function setGamesPlayed($gamesPlayed)
    {
        $this->gamesPlayed = $gamesPlayed;
    }

    public function getGoalsScored()
    {
        return $this->goalsScored;
    }


    public function setGoalsScored($goalsScored)
    {
        $this->goalsScored = $goalsScored;
    }

    public function getCleanSheets()
    {
        return $this->cleanSheets;
    }

    public function setCleanSheets($cleanSheets)
    {
        $this->cleanSheets = $cleanSheets;
    }

    public function getAssists()
    {
        return $this->assists;
    }

    public function setAssists($assists)
    {
        $this->assists = $assists;
    }

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team)
    {
        $this->team = $team;
    }

    public function __toString()
    {
        return (string) $this->getFirstName().' '.$this->getLastName();
    }


}