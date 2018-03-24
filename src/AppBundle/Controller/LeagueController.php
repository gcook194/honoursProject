<?php
namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\LeagueType;
use AppBundle\Constants\Constants as constants;
use AppBundle\Entity\LeagueMeta;
use AppBundle\Entity\League;
use AppBundle\Entity\Fixture;
use AppBundle\Entity\Team;
use AppBundle\Business\LeagueManager;
use AppBundle\Business\LeagueMetaManager;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class LeagueController extends Controller
{
    /**
     * @Route("/createLeague", name="add_league")
     */
    public function createLeague(Request $request, LeagueManager $leagueMgr)
    {
        //get the entity manager to save the league
        $em = $this->getDoctrine()->getManager();

        //build the league from the submitted form data
        $leagueForm = $this->createForm(LeagueType::class);
        $leagueForm->handleRequest($request);

        if ($leagueForm->isSubmitted() && $leagueForm->isValid()) {

            $league = $leagueForm->getData();

            //double check no dodgy user type stuff has happened
            $user = $this->get('security.token_storage')->getToken()->getUser();

            if ($user->getUserType() == constants::USER_TYPE_ADMIN || $user->getUserType() == constants::USER_TYPE_LEAGUE_ADMIN) {

                //Make the current user the league admin if all is good
                $league->setAdministrator($user);

                //save the league
                $leagueMgr->save($league);
            }
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/createLeagueRules", name="addLeagueRules")
     */
    public function createLeagueRules(Request $request, LeagueManager $leagueMgr, LeagueMetaManager $leagueMetaMgr) {

        //get the user and the league for setting administrator and meta, etc
        $user = $this->get('security.token_storage')->getToken()->getUser();
        $league = $this->getDoctrine()->getRepository(League::class)->findOneByAdministrator($user);

        //check the default rules form has been submitted
        $defaultRules = $request->get("defaultRules");

        //grab anything from the form that we might need
        $numberOfTeams = $request->get('numberOfTeams1');
        $roundsOfFixtures = $request->get('roundsOfFixtures');

        //create the league Meta
        $leagueMetaMgr->createLeagueMeta($numberOfTeams, $roundsOfFixtures, $league);

        //$leagueMeta = $this->getDoctrine()->getRepository(LeagueMeta::class)->findByLeague($league);
        $leagueMeta = $league->getLeagueMeta();

        //add teams to the league
        $leagueMgr->addTeams($league);

        //automatically create fixtures for the league
        $roundsOfFixtures = $leagueMeta->getRoundsOfFixtures();

        if ($roundsOfFixtures != null) {

            //get all our teams
            $teams = $this->getDoctrine()->getRepository(Team::class)->findByLeague($league);

            //create fixtures for the league
            $leagueMgr->createLeagueFixtures($teams, $roundsOfFixtures, $league);
        }

        return $this->redirectToRoute('homepage');
    }

    /**
     * @Route("/selectLeague", name="select_league")
     */
    public function selectLeague(Request $request, LeagueManager $leagueMgr, ValidatorInterface $validator)
    {
        $vals = [];

        $league = $request->get("leagueId");
        $accessCode = $request->get("accessCode");

        $repository = $this->getDoctrine()->getRepository(League::class);

        $leagueObj = $repository->find($league);

        if ($leagueObj !== null && !empty($leagueObj) && $leagueObj->getLeagueMeta()->getAccessCode() == $accessCode) {
            if ($this->allTeamsEdited($leagueObj->getTeams())) {
                //get the user
                $user = $this->get('security.token_storage')->getToken()->getUser();

                $user->addLeague($leagueObj);
                $leagueObj->addUser($user);

                $em = $this->getDoctrine()->getManager();
                $em->flush();
            } else {
                //get all leagues for user to choose from
                $leagues = $this->getDoctrine()->getRepository(League::class)->findAll();
                $vals["errorMessage"] = "This league is not in a state to be joined yet.  Please try again at a later date.";
                $vals["step"] = 1;
                $vals["leagues"] = $leagues;

                return $this->render('default/index.html.twig', $vals);
            }
        } else {
            $constraint = new Assert\EqualTo($accessCode);
            $constraint->message = 'Incorrect access code for this league';

            $errorList = $validator->validate($leagueObj->getLeagueMeta()->getAccessCode(), $constraint);

            if (count($errorList) > 0){
                //get all leagues for user to choose from
                $leagues = $this->getDoctrine()->getRepository(League::class)->findAll();

                $vals["errorMessage"] = $errorList[0]->getMessage();
                $vals["step"] = 1;
                $vals["leagues"] = $leagues;

                return $this->render('default/index.html.twig', $vals);
            }
        }

        return $this->redirectToRoute('homepage');
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