<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 27/02/2018
 * Time: 14:30
 */

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Entity\Team;
use AppBundle\Constants\Constants as constants;
use AppBundle\Business\LeagueManager;
use AppBundle\Entity\League;
use AppBundle\Entity\Fixture;
use AppBundle\Entity\Player;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;


class AjaxController extends Controller
{


    /**
     * @Route("/dashboardCharts", name="topScoringTeam")
     * @method("POST")
     */
    public function getDashboardCharts(LeagueManager $leagueMgr) {

        //get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //get the user's league
        if ($user != null && $user->getUserType() == constants::USER_TYPE_LEAGUE_ADMIN) {
            $league = $this->getDoctrine()->getRepository(League::class)->findOneByAdministrator($user);
        } else {
            $league = $user->getLeagues()->getValues()[0];
        }

        $repository = $this->getDoctrine()->getRepository(Fixture::class);

        //TOP SCORING TEAMS
        $query = $repository->createQueryBuilder('f')
            ->where('f.league = :league')
            ->setParameter('league', $league)
            ->orderBy('f.round')
            ->getQuery();

        $fixtures = $query->getResult();

        //build league table
        $table = $leagueMgr->buildTable($league, $fixtures);
        $data = $this->getTopScoringTeams($table);

        //TOP SCORING PLAYERS
        $repository = $this->getDoctrine()->getRepository(Player::class);

        $query = $repository->createQueryBuilder('p')
            ->where('p.goalsScored > 0')
            ->andWhere('p.team in (:league)')
            ->setParameter('league', $league->getTeams())
            ->getQuery();

        $players = $query->getResult();

        //get the events for those matches
        $highestScoringPlayers = $this->getTopScoringPlayers($players);

        return new JsonResponse(array("table" => $data, "scoringPlayers" => $highestScoringPlayers));
    }

    /**
     * @Route("/teamCharts/{id}", name="teamCharts")
     * @method("POST")
     */
    public function getTeamCharts(LeagueManager $leagueMgr, $id) {

        //get the user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //get the user's league
        if ($user != null && $user->getUserType() == constants::USER_TYPE_LEAGUE_ADMIN) {
            $league = $this->getDoctrine()->getRepository(League::class)->findOneByAdministrator($user);
        } else {
            $league = $user->getLeagues()->getValues()[0];
        }

        $repository = $this->getDoctrine()->getRepository(Fixture::class);

        $query = $repository->createQueryBuilder('f')
            ->where('f.league = :league')
            ->setParameter('league', $league)
            ->orderBy('f.round')
            ->getQuery();

        $fixtures = $query->getResult();

        //build league table
        $table = $leagueMgr->buildTable($league, $fixtures);
        $data = [];

        foreach ($table as $entry) {
            if ($entry->getTeam()->getId() == $id) {
                $data["wins"] = $entry->getWins();
                $data["draws"] = $entry->getDraws();
                $data["losses"] = $entry->getLosses();
            }
        }

        return new JsonResponse(array("data" => $data));
    }

    /*
     *Had to make the method like this because I'm rubbish and can't work Symfony serialization properly... :(
     */
    private function getTopScoringTeams($league) {
        $data = [];

        foreach ($league as $entry) {
            $instance = [];
            $instance["team"] = $entry->getTeam()->getName();
            $instance["goals"] = $entry->getScored();
            array_push($data, $instance);
        }

        return $data;
    }

    private function getTopScoringPlayers($players) {
        $data = [];

        foreach ($players as $p) {
            $instance = [];
            $instance["player"] = $p->getFirstName() . ' ' . $p->getLastName();
            $instance["goals"] = $p->getGoalsScored();
            array_push($data, $instance);
        }

        return $data;
    }
}