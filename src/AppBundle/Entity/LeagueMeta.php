<?php

//Class to hold data about win / draw points, number of fixtures, table sort order etc
namespace AppBundle\Entity; 

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="league_meta")
 */
class LeagueMeta {
    
    /**
    * @ORM\Id
    * @ORM\Column(type="integer")
    * @ORM\GeneratedValue(strategy="AUTO")
    */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="HWe need to know how many points to accredit to a win")
     */
    private $win_points;

    /**
     * @ORM\Column(type="integer")
     * * @Assert\NotBlank(message="HWe need to know how many points to accredit to a draw")
     */
    private $draw_points;

    /**
     * @ORM\Column(type="integer")
     * * @Assert\NotBlank(message="HWe need to know how many teams you'd like to add to the league")
     */
    private $number_of_teams;

    /**
     * @ORM\Column(type="string")
     * * @Assert\NotBlank(message="HWe need to know how to sort the league table in the event that two teams are even on points")
     */
    private $sort_col_one;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="HWe need to know how to sort the league table in the event that two teams are even on points and sort column one")
     */
    private $sort_col_two;

    /**
     * @ORM\Column(type="string")
     * @Assert\NotBlank(message="We need to know how to sort the league table in the event that two teams are even on points and sort columns one and two")
     */
    private $sort_col_three;

    /**
     * @ORM\Column(type="string")
     */
    private $access_code;

    /**
     * @ORM\Column(type="integer")
     * * @Assert\NotBlank(message="We need to know how many times you would like each team to play against each other team")
     */
    private $rounds_of_fixtures;

    function __construct() {

        //generate a random 6 digit access code for new leagues
        $digits = 6;
        $this->access_code = rand(pow(10, $digits-1), pow(10, $digits)-1);
    }

	public function getId(){
		return $this->id;
	}

	public function setId($id) {
        $this->id = $id;
    }

    public function getWinPoints() {
        return $this->win_points;
    }

    public function setWinPoints($win_points) {
        $this->win_points = $win_points;
    }

    public function getDrawPoints() {
        return $this->draw_points;
    }

    public function setDrawPoints($draw_points) {
        $this->draw_points = $draw_points;
    }

    public function getNumberOfTeams() {
        return $this->number_of_teams;
    }

    public function setNumberOfTeams($number_of_teams) {
        $this->number_of_teams = $number_of_teams;
    }

    public function getSortColOne() {
        return $this->sort_col_one;
    }

    public function setSortColOne($sort_col_one) {
        $this->sort_col_one = $sort_col_one;
    }

    public function getSortColTwo() {
        return $this->sort_col_two;
    }

    public function setSortColTwo($sort_col_two) {
        $this->sort_col_two = $sort_col_two;
    }

    public function getSortColThree() {
        return $this->sort_col_three;
    }

    public function setSortColThree($sort_col_three) {
        $this->sort_col_three = $sort_col_three;
    }

    public function getAccessCode() {
        return $this->access_code;
    }

    public function setAccessCode($access_code) {
        $this->access_code = $access_code;
    }

    public function getRoundsOfFixtures() {
        return $this->rounds_of_fixtures;
    }

    public function setRoundsOfFixtures($rounds_of_fixtures) {
        $this->rounds_of_fixtures = $rounds_of_fixtures;
    }
}