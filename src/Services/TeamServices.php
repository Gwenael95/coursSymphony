<?php


namespace App\Services;

use App\Entity\Project;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;

class TeamServices
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em=$em;
    }
    /**
     * this function add a team in database after created it with a form
     * @param Team $team
     */
    public function addTeam(Team $team ) {
        $this->em->persist($team);
        $this->em->flush();
    }


    /**
     * this function delete selected teams from database, selected in a form
     * @param $quest
     */
    public function delTeam($quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                foreach ($qst["teamName"] as $teamName) {
                    $team = $this->em->getRepository(Team::class)->findTeamByTeamName($teamName);
                    $this->em->remove($team);
                }
            }
        }
        $this->em->flush();
    }


    /**
     * this function delete a team using it's name
     * @param $teamName
     */
    public function delTeamByName($teamName ) {
        $team = $this->em->getRepository(Team::class)->findTeamByName($teamName);
        $this->em->remove($team);
        $this->em->flush();
    }


    /**
     * this function update one team data
     * @param $id
     * @param Team $newTeam
     */
    public function updateTeam($id ,Team $newTeam) {
        $team = $this->em->getRepository(Team::class)->findTeamById($id);
        $team->setTeamName($newTeam->getTeamName());
        $this->em->persist($team);
        $this->em->flush();
    }


    /**
     * this function get team id from database from a select with attributes multiple
     * @param $quest
     * @return int|null
     */
    public function getTeamIdSelectMultiple($quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                foreach ($qst["teamName"] as $teamName) {
                    $team = $this->em->getRepository(Team::class)->findTeamByTeamName($teamName);
                    return $team->getId();
                }
            }
        }
        return null;
    }


    /**
     * this function get team id from database from a select with attributes multiple=false
     * @param $quest
     * @return int|null
     */
    public function getTeamIdSelectUnique($quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $this->em->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                return $team->getId();
            }
        }
        return null;
    }


    /**
     * this function get all team from database
     * @return mixed
     */
    public function getAllTeam(){
        return $this->em->getRepository(Team::class)->findAllTeam();
    }


    /**
     * this function get team from database depending on the given id
     * @param $id
     * @return mixed
     */
    public function getTeamById($id){
        return $this->em->getRepository(Team::class)->findTeamById($id);
    }


    /**
     * this function assign team and projects and save this in database
     * @param $quest
     */
    public function assignTeamProject($quest){
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $this->em->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                $this->em->persist($team);
                if (isset($qst["name"])) {
                    foreach ($qst["name"] as $projectName) {
                        $newProject= $this->em->getRepository(Project::class)->findProjectByprojectName($projectName);
                        $newProject->addTeam($team);
                        $this->em->persist($newProject);
                    }
                }
            }
        }
        $this->em->flush();
    }


    /**
     * this function assign team and projects and save this in database
     * @param $quest
     */
    public function disassignProject($quest){
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $this->em->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                $this->em->persist($team);
                if (isset($qst["name"])) {
                    foreach ($qst["name"] as $projectName) {
                        $newProject= $this->em->getRepository(Project::class)->findProjectByprojectName($projectName);
                        $newProject->removeTeam($team);
                        $this->em->persist($newProject);
                    }
                }
            }
        }
        $this->em->flush();
    }

}