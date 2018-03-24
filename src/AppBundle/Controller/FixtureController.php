<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 19/01/2018
 * Time: 22:04
 */

namespace AppBundle\Controller;

use AppBundle\Business\FixtureManager;
use AppBundle\Entity\FixtureEvent;
use AppBundle\Form\FixtureEventType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Fixture;
use AppBundle\Entity\Team;
use AppBundle\Constants\Constants as constants;
use AppBundle\Form\FixtureUpdateType;


class FixtureController extends Controller
{
    /**
     * @Route("/editFixture/{id}", name="edit_fixture")
     */
    public function showEditFixture($id) {

        $vals = [];

        //Get the fixture
        $fixture = $this->getDoctrine()->getRepository(Fixture::class)->findOneById($id);

        $fixtureForm = $this->createForm(FixtureUpdateType::class, $fixture, array(
            'action' => '/doEditFixture/'.$fixture->getId(),
            'method' => 'POST',
        ));

        $vals["fixture"] = $fixture;
        $vals["fixtureForm"] = $fixtureForm->createView();

        return $this->render('editFixture.html.twig', $vals);
    }

    /**
     * @Route("/doEditFixture/{id}", name="do_edit_fixture")
     * @method("POST")
     */
    public function doEditFixture(Request $request, Fixture $fixture, FixtureManager $fixtureMgr) {
        $fixtureForm = $this->createForm(FixtureUpdateType::class, $fixture);
        $fixtureForm->handleRequest($request);

        if ($fixtureForm->isSubmitted() && $fixtureForm->isValid()) {
            $fixture->setPlayed(true);
            $fixtureMgr->save($fixture);

            return $this->redirectToRoute("homepage");
        }

        $vals = [];
        $vals["fixture"] = $fixture;
        $vals["fixtureForm"] = $fixtureForm->createView();

        return $this->render('editFixture.html.twig', $vals);
    }

    /**
     * @Route("/fixture/{id}/{referrer}/{referrerId}", name="show_fixture")
     */
    public function showFixtureInfo($referrer, $referrerId, $id) {

        //values
        $vals = [];

        //Get the fixture
        $fixture = $this->getDoctrine()->getRepository(Fixture::class)->findOneById($id);

        if (strcmp($referrer, constants::URL_REFERRER_LEAGUE) === 0) {
            $vals["referrer"] = $fixture->getLeague();
        } else {
            $vals["referrer"] = $this->getDoctrine()->getRepository(Team::class)->findOneById($referrerId);
        }

        //get the last five fixtures for each side
        $repository = $this->getDoctrine()->getRepository(Fixture::class);

        //home team
        $query = $repository->createQueryBuilder('f')
            ->where('f.played = 1')
            ->andWhere('f.homeTeam = :team OR f.awayTeam = :team')
            ->setParameter('team', $fixture->getHomeTeam())
            ->setMaxResults(4)
            ->getQuery();

        $homeResults = $query->getResult();

        //away team
        $query = $repository->createQueryBuilder('f')
            ->where('f.played = 1')
            ->andWhere('f.homeTeam = :team OR f.awayTeam = :team')
            ->setParameter('team', $fixture->getAwayTeam())
            ->setMaxResults(4)
            ->getQuery();

        $awayResults = $query->getResult();

        $vals["fixture"] = $fixture;
        $vals["homeResults"] = $homeResults;
        $vals["awayResults"] = $awayResults;

        return $this->render('fixtureInfo.html.twig', $vals);
    }

    /**
    * @Route("/editFixtureEvent/{id}/{referrer}/{referrerId}", name="edit_fixture_events")
    * @method("POST")
    */
    public function doEditFixtureEvents($id, $referrer, $referrerId, Request $request) {

        //values
        $vals = [];

        //Get the fixture
        $fixture = $this->getDoctrine()->getRepository(Fixture::class)->findOneById($id);

        if (strcmp($referrer, constants::URL_REFERRER_LEAGUE) === 0) {
            $vals["referrer"] = $fixture->getLeague();
        } else {
            $vals["referrer"] = $this->getDoctrine()->getRepository(Team::class)->findOneById($referrerId);
        }

        $vals["reffererId"] = $referrerId;
        $vals["fixture"] = $fixture;

        $fixtureEvent = new FixtureEvent();
        $fixtureEvent->setFixture($fixture);

        $form = $this->createForm(FixtureEventType::class, $fixtureEvent);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $event = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $player = $event->getPlayer();

            if (strcmp($event->getEventType(), "goal") === 0) {
                $player->setGoalsScored($player->getGoalsScored() + 1);
            }

            if (strcmp($event->getEventType(), "assist") === 0) {
                $player->setAssists($player->getAssists() + 1);
            }

            $em->persist($event);
            $em->flush();

            $this->addFlash(
                'notice',
                'Your changes were saved!'
            );
            return $this->redirectToRoute('fixture_events', array('referrer'=>$referrer, 'referrerId'=>$referrerId, 'id'=>$id));
        }

        $vals["form"] = $form->createView();

        return $this->render('fixtureEvents.html.twig', $vals);
    }

    /**
    * @Route("/fixture/{id}/{referrer}/{referrerId}/events", name="fixture_events")
    */
    public function showEditFixtureEvents($referrer, $referrerId, $id, Request $request) {
        //values
        $vals = [];

        //Get the fixture
        $fixture = $this->getDoctrine()->getRepository(Fixture::class)->findOneById($id);

        if (strcmp($referrer, constants::URL_REFERRER_LEAGUE) === 0) {
            $vals["referrer"] = $fixture->getLeague();
        } else {
            $vals["referrer"] = $this->getDoctrine()->getRepository(Team::class)->findOneById($referrerId);
        }

        $vals["fixture"] = $fixture;

        $fixtureEvent = new FixtureEvent();
        $fixtureEvent->setFixture($fixture);

        $form = $this->createForm(FixtureEventType::class, $fixtureEvent, array(
            'action' => $this->generateUrl('edit_fixture_events', array('id' => $fixture->getId(), 'referrer' =>$referrer, 'referrerId' => $referrerId)),
            'method' => 'POST',
        ));

        $vals["form"] = $form->createView();

        return $this->render('fixtureEvents.html.twig', $vals);
    }
}