<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 08/01/2018
 * Time: 17:04
 */

namespace AppBundle\Entity;


class LeagueTableEntry
{
    private $team;
    private $wins = 0;
    private $draws = 0;
    private $losses = 0;
    private $scored = 0;
    private $conceded = 0;
    private $goalDifference = 0;
    private $points = 0;
    private $league;
    private $form = [];
    private $played = 0;

    public function getTeam()
    {
        return $this->team;
    }

    public function setTeam($team)
    {
        $this->team = $team;
    }

    public function getWins()
    {
        return $this->wins;
    }

    public function setWins($wins)
    {
        $this->wins = $wins;
    }

    public function getDraws()
    {
        return $this->draws;
    }

    public function setDraws($draws)
    {
        $this->draws = $draws;
    }

    public function getLosses()
    {
        return $this->losses;
    }

    public function setLosses($losses)
    {
        $this->losses = $losses;
    }

    public function getScored()
    {
        return $this->scored;
    }

    public function setScored($scored)
    {
        $this->scored = $scored;
    }

    public function getConceded()
    {
        return $this->conceded;
    }

    public function setConceded($conceded)
    {
        $this->conceded = $conceded;
    }

    public function getGoalDifference()
    {
        return $this->goalDifference;
    }

    public function setGoalDifference($goalDifference)
    {
        $this->goalDifference = $goalDifference;
    }

    public function getPoints()
    {
        return $this->points;
    }

    public function setPoints($points)
    {
        $this->points = $points;
    }

    public function getLeague()
    {
        return $this->league;
    }

    public function setLeague($league)
    {
        $this->league = $league;
    }

    public function getForm()
    {
        return $this->form;
    }

    public function setForm($form)
    {
        $this->form = $form;
    }

    public function getPlayed()
    {
        return $this->played;
    }

    public function setPlayed($played)
    {
        $this->played = $played;
    }
}