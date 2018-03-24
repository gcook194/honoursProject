<?php
/**
 * Created by PhpStorm.
 * User: Gavin
 * Date: 17/01/2018
 * Time: 23:21
 */

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

class TeamManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    //extract table entry for specific team
    public function getTableEntryForTeam($leagueTable, $team) {
        $entry = new LeagueTableEntry();

        foreach ($leagueTable as $tableEntry) {
            if ($tableEntry->getTeam()->GetId() == $team->GetId()) {
                $entry = $tableEntry;
            }
        }

        return $entry;
    }

    //check if any teams are missing a badge image
    public function allTeamsHaveBadges($teams) {
        foreach ($teams as $team) {
            if ($team->getBadgeImage() === null && ! empty($team->getBadgeImage())) {
                return false;
            }
        }

        return true;
    }

    public function save($team) {
        $this->em->persist($team);
        $this->em->flush($team);
    }

    public function addPlayer($player) {
        $this->em->persist($player);
        $this->em->flush($player);
    }
}