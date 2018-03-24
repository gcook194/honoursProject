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

class LeagueManager
{
    private $em;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function save($league) {
        $this->em->persist($league);
        $this->em->flush($league);
    }

    //function to add a number of teams to a given league
    public function addTeams($league) {
        for ($i = 0; $i < $league->getLeagueMeta()->getNumberOfTeams(); $i++) {
            $team = new Team();
            $team->setName(constants::DEFAULT_TEAM_NAME.($i + 1));
            $team->setNickname("");
            $team->setLeague($league);
            $team->setTwitterUrl("");
            $team->setWebsiteUrl("");
            $team->setBadgeImage("");

            try {
                $this->em->persist($team);
                $this->em->flush();
            } catch (Exception $e) {
                die($e->getTraceAsString());
            }
        }
    }

    public function createLeagueFixtures($teams, $roundsOfFixtures, $league) {

        //loop round the number of rounds of fixtures.
        //A "round of fixtures" is the number of fixtures it takes each team to play every other team
        for ($i = 0; $i < $roundsOfFixtures; $i++) {

            /*
             * Ensure fixtures flip if more than one round
             * e.g. :
             * Round 1: Team 1 vs Team 2
             * Round 2: Team 2 vs Team 1
             */
            if ($i % 2 == 0) {
                $homeTeams = $teams;
                $awayTeams = array_reverse($teams);
                $fixtures = [];
            } else {
                $homeTeams = array_reverse($teams);
                $awayTeams = $teams;
                $fixtures = [];
            }

            //Create a new fixture for every possible match up
            foreach ($homeTeams as $team) {
                foreach ($awayTeams as $opponent) {
                    if ($team !== $opponent) {

                        //set up some empty fixtures
                        $fixture = new Fixture();
                        $fixture->setLeague($league);
                        $fixture->setHomeTeam($team);
                        $fixture->setAwayTeam($opponent);
                        $fixture->setRound(($i + 1));
                        $fixture->setPlayed(false);

                        //A round of fixtures only requires each team to play each other team once
                        //so we must de-dupe the array
                        $fixtureExists = $this->fixtureExistsInList($fixtures, $fixture);

                        if (!$fixtureExists) {
                            array_push($fixtures, $fixture);
                        }
                    }
                }
            }

            //save any de-duped fixtures
            foreach ($fixtures as $f) {
                $this->em->persist($f);
                $this->em->flush();
            }
        }
    }

    public function buildTable(League $league, $fixtures)
    {
        //new array of table entries
        $table = [];

        //if we have some fixtures
        if (!$this->zeroGamesPlayed($fixtures)) {

            //get the teams and iterate round them
            $teams = $league->getTeams();

            foreach ($teams as $team) {

                $tableEntry = new LeagueTableEntry();

                //set the team
                $tableEntry->setTeam($team);

                //set an array of form
                $form = [];

                //work out results for each fixture
                foreach ($fixtures as $fixture) {

                    //if the current team is the home team
                    if ($fixture->getHomeTeam() == $team && $fixture->getHomeGoals() !== null) {

                        //set the league to this league
                        $tableEntry->setLeague($league);

                        //set up goals scored, conceded and goal difference
                        $tableEntry->setScored($tableEntry->getScored() + $fixture->getHomeGoals());
                        $tableEntry->setConceded($tableEntry->getConceded() + $fixture->getAwayGoals());
                        $tableEntry->setGoalDifference($tableEntry->getScored() - $tableEntry->getConceded());
                        $tableEntry->setPlayed($tableEntry->getPlayed()+1);

                        //determine if home team won, lost, or drew
                        if ($fixture->getHomeGoals() > $fixture->getAwayGoals()) {

                            //increase number of wins
                            $tableEntry->setWins($tableEntry->getWins() + 1);

                            //increase points by whatever number is specified
                            $tableEntry->setPoints($tableEntry->getPoints() + constants::DEFAULT_WIN_POINTS);

                            //add to team form
                            array_push($form, "W");

                        } else if ($fixture->getHomeGoals() < $fixture->getAwayGoals()) {

                            //increase number of losses
                            $tableEntry->setLosses($tableEntry->getLosses() + 1);

                            //add to team form
                            array_push($form, "L");

                        } else if ($fixture->getHomeGoals() == $fixture->getAwayGoals()) {

                            //increase number of draws
                            $tableEntry->setDraws($tableEntry->getDraws() +1);

                            //add points for a draw to the entry
                            $tableEntry->setPoints($tableEntry->getPoints() + constants::DEFAULT_DRAW_POINTS);

                            //add to team form
                            array_push($form, "D");

                        }
                    } else {

                        //if the current team is the home team
                        if ($fixture->getAwayTeam() == $team && $fixture->getHomeGoals() !== null) {

                            //set the league to this league
                            $tableEntry->setLeague($league);

                            //set up goals scored, conceded and goal difference
                            $tableEntry->setScored($tableEntry->getScored() + $fixture->getAwayGoals());
                            $tableEntry->setConceded($tableEntry->getConceded() + $fixture->getHomeGoals());
                            $tableEntry->setGoalDifference($tableEntry->getScored() - $tableEntry->getConceded());
                            $tableEntry->setPlayed($tableEntry->getPlayed()+1);

                            //determine if home team won, lost, or drew
                            if ($fixture->getHomeGoals() > $fixture->getAwayGoals()) {

                                //increase number of wins
                                $tableEntry->setLosses($tableEntry->getLosses() + 1);

                                //add to team form
                                array_push($form, "L");

                            } else if ($fixture->getHomeGoals() < $fixture->getAwayGoals()) {

                                //increase number of losses
                                $tableEntry->setWins($tableEntry->getWins() + 1);

                                //increase points by defined number
                                $tableEntry->setPoints($tableEntry->getPoints() + constants::DEFAULT_WIN_POINTS);

                                //add to team form
                                array_push($form, "W");

                            } else if ($fixture->getHomeGoals() == $fixture->getAwayGoals()) {

                                //increase number of draws
                                $tableEntry->setDraws($tableEntry->getDraws() + 1);

                                //add points for a draw to the entry
                                $tableEntry->setPoints($tableEntry->getPoints() + constants::DEFAULT_DRAW_POINTS);

                                //add to team form
                                array_push($form, "D");
                            }
                        }
                    }
                }

                //set the form
                $tableEntry->setForm($form);

                //add the team to the league table
                array_push($table, $tableEntry);

                //sort the table
                usort($table, array($this, "compareTo"));

                //flip it because it will be upside down
                $table = array_reverse($table);
            }
        } else {
            $table = $this->emptyTableSort($league->getTeams()->toArray());
        }

        return $table;
    }

    private function compareTo(LeagueTableEntry $a, LeagueTableEntry $o)
    {
        $comparison = 0;

        if ($a->getPoints() > $o->getPoints()) {
            $comparison = 1;
        } else if ($a->getPoints() < $o->getPoints()) {
            $comparison = -1;
        } else {
            if ($a->getGoalDifference() > $o->getGoalDifference()) {
                $comparison = 1;
            } else if ($a->getGoalDifference() < $o->getGoalDifference()) {
                $comparison = -1;
            } else if ($a->getScored() > $o->getScored()) {
                $comparison = 1;
            } else if ($a->getScored() < $o->getScored()) {
                $comparison = -1;
            } else {
                if (strcmp($a->getTeam()->getName(), $o->getTeam()->getName()) > 0) {
                    $comparison = -1;
                } else if (strcmp($a->getTeam()->getName(), $o->getTeam()->getName()) < 0) {
                    $comparison = 1;
                }
            }
        }

        return $comparison;
    }

    //return an empty sorted league table
    private function emptyTableSort($teams) {
        //set up a league table
        $table = [];

        //sort it alphabetically
        sort($teams);

        //loop round each team and create a table entry in that order
        foreach ($teams as $team) {
            $tableEntry = new LeagueTableEntry();

            $tableEntry->setTeam($team);
            $tableEntry->setLeague($team->getLeague());
            $tableEntry->setWins(0);
            $tableEntry->setLosses(0);
            $tableEntry->setDraws(0);
            $tableEntry->setPoints(0);
            $tableEntry->setPlayed(0);
            $tableEntry->setScored(0);
            $tableEntry->setConceded(0);
            $tableEntry->setGoalDifference(0);

            array_push($table, $tableEntry);
        }

        return $table;
    }

    //check whether any matches have been played
    private function zeroGamesPlayed($fixtures) {
        foreach ($fixtures as $fixture) {
            if ($fixture->getHomeGoals() != null) {
                return false;
            }

            if ($fixture->getAwayGoals() != null) {
                return false;
            }
        }

        return true;
    }

    //ensure the fixtures are not being duplicated
    private function fixtureExistsInList($fixtures, $newFixture) {

        foreach ($fixtures as $savedFixture) {
            if ($newFixture->getHomeTeam() === $savedFixture->getHomeTeam()) {
                if ($newFixture->getAwayTeam() === $savedFixture->getAwayTeam()) {
                    return true;
                }
            } else if ($newFixture->getHomeTeam() === $savedFixture->getAwayTeam()) {
                if ($newFixture->getAwayTeam() === $savedFixture->getHomeTeam()) {
                    return true;
                }
            }
        }

        return false;
    }
}