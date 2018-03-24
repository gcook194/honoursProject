<?php

namespace Tests\AppBundle\Business;

use PHPUnit\Framework\TestCase;
use AppBundle\Entity\LeagueTableEntry;
use AppBundle\Constants\Constants as constants;
use AppBundle\Entity\Fixture;
use AppBundle\Entity\Team;
use AppBundle\Entity\League;
use AppBundle\Entity\LeagueMeta;
use AppBundle\Business\LeagueManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class LeagueManagerTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel();

        $this->em = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    //Test case to ensure that the logic within LeagueManager works correctly
    public function testAdd()
    {
        $leagueMgr = new LeagueManager($this->em);

        //create a test league
        $league = new League();
        $league->setId(1);
        $league->setName("Test Case League 1");

        $this->em->persist($league);

        //add some meta
        $leagueMeta = new LeagueMeta();
        $leagueMeta->setRoundsOfFixtures(1);
        $leagueMeta->setAccessCode("123456");
        $leagueMeta->setNumberOfTeams(4);
        $leagueMeta->setWinPoints(3);
        $leagueMeta->setDrawPoints(1);
        $leagueMeta->setSortColOne("");
        $leagueMeta->setSortColTwo("");
        $leagueMeta->setSortColThree("");

        //Add the meta to the league
        $league->setLeagueMeta($leagueMeta);
        $this->em->persist($leagueMeta);

        $this->em->flush();

        //This is where the TEST logic starts.  Create teams
        $leagueMgr->addTeams($league);

        //Call to the database and get the new updated league object
        $league = $this->em
            ->getRepository(League::class)
            ->findOneById(1)
        ;

        //CHECK whether there are 5 teams in the league (0,1,2,3,4)
        $this->assertEquals(4, count($league->getTeams()->getValues()));

    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}