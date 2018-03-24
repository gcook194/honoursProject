<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LeagueRepository")
 * @ORM\Table(name="fixture_events")
 */
class FixtureEvent
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Column can be an integer as we only require a number between 1 and 90
     * @ORM\Column(type="integer", name="time")
     */
    private $time;

    /**
     * @ORM\ManyToOne(targetEntity="Player")
     * @ORM\JoinColumn(name="player_id", referencedColumnName="id")
     */
    private $player;

    /**
     * @ORM\Column(name="event_type", type="string")
     */
    private $eventType;

    /**
     * @ORM\ManyToOne(targetEntity="Fixture", inversedBy="events")
     * @ORM\JoinColumn(name="fixture_id", referencedColumnName="id")
     */
    private $fixture;

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getTime()
    {
        return $this->time;
    }

    public function setTime($time)
    {
        $this->time = $time;
    }

    public function getPlayer()
    {
        return $this->player;
    }

    public function setPlayer($player)
    {
        $this->player = $player;
    }

    public function getEventType()
    {
        return $this->eventType;
    }

    public function setEventType($eventType)
    {
        $this->eventType = $eventType;
    }

    public function getFixture()
    {
        return $this->fixture;
    }

    public function setFixture($fixture)
    {
        $this->fixture = $fixture;
    }
}