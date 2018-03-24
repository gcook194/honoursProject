<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\League;
use AppBundle\Entity\Fixture;
use AppBundle\Form\LeagueType;
use AppBundle\Form\LeagueMetaType;
use AppBundle\Entity\LeagueMeta;
use AppBundle\Constants\Constants as constants;
use AppBundle\Business\LeagueManager;
use AppBundle\Business\TeamManager;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(LeagueManager $leagueMgr, TeamManager $teamMgr)
    {
        //array to pass back to view
        $vals = array();

        //get the logged in user
        $user = $this->get('security.token_storage')->getToken()->getUser();

        //check if the user is a league admin
        if ($user->getUserType() == constants::USER_TYPE_LEAGUE_ADMIN) {

            //if they are, make sure they have created a league
            $league = $this->getDoctrine()->getRepository(League::class)->findOneByAdministrator($user);

            //if they haven't, add the form to create one.
            if ($league == null) {

                $league = new League();
                $leagueForm = $this->createForm(LeagueType::class, $league, array(
                    'action' => '/createLeague',
                    'method' => 'POST',
                ));

                $vals["leagueForm"] = $leagueForm->createView();
                $vals["step"] = 1;

            } else if ($league->getLeagueMeta() == null) {

                //if league has been created but no rules set
                $leagueMeta = new LeagueMeta();
                $leagueMetaForm = $this->createForm(LeagueMetaType::class, $leagueMeta, array(
                    'action' => '/addLeagueRules',
                    'method' => 'POST',
                ));

                $vals["metaForm"] = $leagueMetaForm->createView();
                $vals["step"] = 2;

            } else if ($league->getLeagueMeta() != null) {

                //grab the newly created teams and return them in the view for editing
                $teams = $this->getDoctrine()->getRepository(Team::class)->findByLeague($league);

                //check whether all teams have been edited or not
                $allTeamsEdited = $this->allTeamsEdited($teams);

                //get list of upcoming matches for the dashboard
                $repository = $this->getDoctrine()->getRepository(Fixture::class);

                $query = $repository->createQueryBuilder('f')
                    ->where('f.played = 0')
                    ->andWhere('f.league = :league')
                    ->setParameter('league', $league)
                    ->getQuery();

                $upcomingMatches = $query->getResult();

                //get list of results for the dashboard
                $query = $repository->createQueryBuilder('f')
                    ->where('f.played = 1')
                    ->andWhere('f.league = :league')
                    ->setParameter('league', $league)
                    ->getQuery();

                $results = $query->getResult();

                //get all matches to build league table
                $query = $repository->createQueryBuilder('f')
                    ->where('f.league = :league')
                    ->setParameter('league', $league)
                    ->orderBy('f.round')
                    ->getQuery();

                $fixtures = $query->getResult();

                //build league table
                $table = $leagueMgr->buildTable($league, $fixtures);

                //have all teams got badges?
                $allTeamsHaveBadges = $teamMgr->allTeamsHaveBadges($teams);

                //if they have then pass the league
                $vals["league"] = $league;
                $vals["teams"] = $teams;
                $vals["fixtures"] = $fixtures;
                $vals["table"] = $table;
                $vals["allTeamsHaveBadges"] = $allTeamsHaveBadges;
                $vals["upcomingMatches"] = $upcomingMatches;
                $vals["results"] = $results;

                if (!$allTeamsEdited)
                {
                    $vals["step"] = 3;
                } else {
                    $vals["step"] = 4;
                }
            }
        } else {

            if (empty($user->getLeagues()->getValues())) {
                //get all leagues for user to choose from
                $leagues = $this->getDoctrine()->getRepository(League::class)->findAll();

                $vals["step"] = 1;
                $vals["leagues"] = $leagues;
            } else {

                $vals["step"] = 2;

                $leagues = $user->getLeagues();
                $league = $leagues[0];

                //grab the newly created teams and return them in the view for editing
                $teams = $this->getDoctrine()->getRepository(Team::class)->findByLeague($league);

                //get list of upcoming matches for the dashboard
                $repository = $this->getDoctrine()->getRepository(Fixture::class);

                $query = $repository->createQueryBuilder('f')
                    ->where('f.played = 0')
                    ->andWhere('f.league = :league')
                    ->setParameter('league', $league)
                    ->getQuery();

                $upcomingMatches = $query->getResult();

                //get list of results for the dashboard
                $query = $repository->createQueryBuilder('f')
                    ->where('f.played = 1')
                    ->andWhere('f.league = :league')
                    ->setParameter('league', $league)
                    ->getQuery();

                $results = $query->getResult();

                //get all matches to build league table
                $query = $repository->createQueryBuilder('f')
                    ->where('f.league = :league')
                    ->setParameter('league', $league)
                    ->orderBy('f.round')
                    ->getQuery();

                $fixtures = $query->getResult();

                //build league table
                $table = $leagueMgr->buildTable($league, $fixtures);

                //have all teams got badges?
                $allTeamsHaveBadges = $teamMgr->allTeamsHaveBadges($teams);

                //if they have then pass the league
                $vals["league"] = $league;
                $vals["teams"] = $teams;
                $vals["fixtures"] = $fixtures;
                $vals["table"] = $table;
                $vals["allTeamsHaveBadges"] = $allTeamsHaveBadges;
                $vals["upcomingMatches"] = $upcomingMatches;
                $vals["results"] = $results;
            }

        }

        return $this->render('default/index.html.twig', $vals);
    }

    //if the name of any team contains the default assigned name, return false
    private function allTeamsEdited($teams)
    {
        foreach ($teams as $team)
        {
            if (strpos($team->getName(), constants::DEFAULT_TEAM_NAME) !== false)
            {
                return false;
            }
        }

        return true;
    }
}
