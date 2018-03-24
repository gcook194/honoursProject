<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 16/01/2018
 * Time: 16:35
 */

namespace AppBundle\Business;

use AppBundle\Entity\LeagueMeta;
use AppBundle\Constants\Constants as constants;
use Doctrine\ORM\EntityManager;

class LeagueMetaManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function createLeagueMeta($numberOfTeams, $roundsOfFixtures, $league) {
        //Set all values based on constants (except number of teams and rounds of fixtures)
        $leagueMeta = new LeagueMeta();

        $leagueMeta->setWinPoints(constants::DEFAULT_WIN_POINTS);
        $leagueMeta->setDrawPoints(constants::DEFAULT_DRAW_POINTS);
        $leagueMeta->setNumberOfTeams($numberOfTeams);
        $leagueMeta->setSortColOne(constants::DEFAULT_SORT_COL_1);
        $leagueMeta->setSortColTwo(constants::DEFAULT_SORT_COL_2);
        $leagueMeta->setSortColThree(constants::DEFAULT_SORT_COL_3);
        $leagueMeta->setRoundsOfFixtures($roundsOfFixtures);

        //Save the league Meta
        $this->em->persist($leagueMeta);
        $this->em->flush();

        //update the league
        $league->setLeagueMeta($leagueMeta);
        $this->em->persist($league);
        $this->em->flush();
    }
}