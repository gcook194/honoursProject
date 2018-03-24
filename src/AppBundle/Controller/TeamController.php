<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Team;
use AppBundle\Business\TeamManager;
use AppBundle\Business\LeagueManager;
use AppBundle\Entity\Fixture;
use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Form\TeamUpdateType;
use AppBundle\Form\PlayerType;
use AppBundle\Entity\Player;

class TeamController extends Controller
{

    /**
     * @Route("/editTeam/{id}", name="edit_team", requirements={"page"="\d+"})
     */
    public function showEditTeam(Request $request, $id)
    {
        //find the team by the ID
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneById($id);

        if ($team != null)
        {
            //have to set the image for the form before submission as it expects either a null or a file, not a string
            if ($team->getBadgeImage() !== null && !empty($team->getBadgeImage())) {
                $image = new File($this->getParameter('badges_directory').'/'.$team->getBadgeImage());
                $team->setBadgeImage($image);
            }

            //make da form
            $teamForm = $this->createForm(TeamUpdateType::class, $team, array(
                'action' => '/doEditTeam/'.$team->getId(),
                'method' => 'POST',
            ));
        }

        $vals = [];
        $vals["teamForm"] = $teamForm->createView();
        $vals["team"] = $team;

        return $this->render('editTeam.html.twig', $vals);
    }

    /**
     * @Route("/doEditTeam/{id}", name="do_edit_team")
     * @Method("POST")
     */
    public function doEditTeam(Request $request, Team $team)
    {
        //have to set the image for the form before submission as it expects either a null or a file, not a string
        if ($team->getBadgeImage() !== null && !empty($team->getBadgeImage())) {
            $image = new File($this->getParameter('badges_directory').'/'.$team->getBadgeImage());
            $team->setBadgeImage($image);
        }

        $teamForm = $this->createForm(TeamUpdateType::class, $team);
        $teamForm->handleRequest($request);

        if ($teamForm->isSubmitted() && $teamForm->isValid())
        {
            //direct back to the team page if the back button was clicked
            if ($teamForm->get('back')->isClicked()) {
                return $this->redirectToRoute('view_team', array("id"=>$team->getId()));
            }

            /** @var Symfony\Component\HttpFoundation\File\UploadedFile $file */
            $file = $team->getBadgeImage();

            if ($file !== null) {
                // Generate a unique name for the file before saving it
                $fileName = md5(uniqid()).'.'.$file->guessExtension();

                // Move the file to the directory where badges are stored
                $file->move(
                    $this->getParameter('badges_directory'),
                    $fileName
                );

                //set the teams badge
                $team->setBadgeImage($fileName);
            } else {

                if (isset($image) && $image !== null ) {

                    // Generate a unique name for the file before saving it
                    $fileName = md5(uniqid()).'.'.$image->guessExtension();

                    // Move the file to the directory where badges are stored
                    $image->move(
                        $this->getParameter('badges_directory'),
                        $fileName
                    );

                    //set the teams badge
                    $team->setBadgeImage($fileName);
                }
            }

            $em = $this->getDoctrine()->getManager();
            $em->flush();
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/team/{id}", name="view_team")
     */
    public function showViewTeam($id, TeamManager $teamMgr, LeagueManager $leagueMgr) {
        $vals = [];

        //find the team by the ID
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneById($id);

        //get the league
        $league = $team->getLeague();

        //get list of teams
        $teams = $this->getDoctrine()->getRepository(Team::class)->findByLeague($league);

        //get list of fixtures for the league
        $fixtures = $this->getDoctrine()->getRepository(Fixture::class)->findByLeague($league);

        //build league table
        $table = $leagueMgr->buildTable($league, $fixtures);

        //get table entry for team
        $teamEntry = $teamMgr->getTableEntryForTeam($table, $team);

        //check if teams have badges
        $allTeamsHaveBadges = $teamMgr->allTeamsHaveBadges($teams);

        //get a lis of fixtures for this team
        $repository = $this->getDoctrine()->getRepository(Fixture::class);

        $query = $repository->createQueryBuilder('f')
            ->where('f.played = 0')
            ->andWhere('(f.homeTeam = :team) OR (f.awayTeam = :team)')
            ->setParameter('team', $team)
            ->getQuery();

        $teamFixtures = $query->getResult();

        //get a list of results for the team
        $query = $repository->createQueryBuilder('f')
            ->where('(f.homeTeam = :team OR f.awayTeam = :team) AND f.played = 1')
            ->setParameter('team', $team)
            ->getQuery();

        $results = $query->getResult();

        $vals["team"] = $team;
        $vals["league"] = $league;
        $vals["teams"] = $teams;
        $vals["teamEntry"] = $teamEntry;
        $vals["table"] = $table;
        $vals["allTeamsHaveBadges"] = $allTeamsHaveBadges;
        $vals["teamFixtures"] = $teamFixtures;
        $vals["teamResults"] = $results;
        $vals["players"] = $team->getPlayers();

        return $this->render('showTeam.html.twig', $vals);
    }

    /**
 * @Route("/addPlayer/{id}", name="view_add_player")
 */
    public function showAddPlayer($id) {

        $vals = [];

        //find the team by the ID
        $team = $this->getDoctrine()->getRepository(Team::class)->findOneById($id);

        //create the form to add players
        $playerForm = $this->createForm(PlayerType::class, new Player(), array(
            'action' => '/doAddPlayer/'.$team->getId(),
            'method' => 'POST',
        ));

        $vals["playerForm"] = $playerForm->createView();
        $vals["team"] = $team;

        return $this->render('addPlayer.html.twig', $vals);

    }

    /**
     * @Route("/doAddPlayer/{id}", name="add_player")
     * @Method("POST")
     */
    public function doAddPlayer(Team $team, Request $request) {

        $playerForm = $this->createForm(PlayerType::class);
        $playerForm->handleRequest($request);

        //if the form is submitted and valid save the player
        if ($playerForm->isSubmitted() && $playerForm->isValid()) {
            $player = $playerForm->getData();
            $player->setTeam($team);

            $em = $this->getDoctrine()->getManager();
            $em->persist($player);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );

            return $this->redirectToRoute('view_team', array('id' => $team->getId()));
        }

        //otherwise pap thine user back to whence thy came
        $vals = [];
        $vals["team"] = $team;
        $vals["playerForm"] = $playerForm->createView();

        return $this->render('addPlayer.html.twig', $vals);
    }
}