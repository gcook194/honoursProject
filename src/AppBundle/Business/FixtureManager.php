<?php

namespace AppBundle\Business;

use AppBundle\Entity\League;
use AppBundle\Repository\LeagueRepository;
use Doctrine\ORM\EntityManagerInterface;
use AppBundle\Entity\User;
use AppBundle\Entity\LeagueTableEntry;
use AppBundle\Constants\Constants as constants;
use AppBundle\Entity\Fixture;
use AppBundle\Entity\Team;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Config\Definition\Exception\Exception;

class FixtureManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function save($fixture) {
        $this->em->persist($fixture);
        $this->em->flush($fixture);
    }
}