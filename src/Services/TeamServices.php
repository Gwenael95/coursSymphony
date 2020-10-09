<?php


namespace App\Services;


use App\Entity\Project;
use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;

class TeamServices
{

    /**
     * this function add a team in database after created it with a form
     * @param ObjectManager $entityManager
     * @param Team $team
     */
    public function addTeam(ObjectManager $entityManager, Team $team ) {
        $entityManager->persist($team);
        $entityManager->flush();
    }


    /**
     * this function delete selected teams from database, selected in a form
     * @param ObjectManager $entityManager
     * @param $quest
     */
    public function delTeam(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                foreach ($qst["teamName"] as $teamName) {
                    $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($teamName);
                    $entityManager->remove($team);
                }
            }
        }
        $entityManager->flush();
    }


    /**
     * this function delete a team using it's name
     * @param ObjectManager $entityManager
     * @param $teamName
     */
    public function delTeamByName(ObjectManager $entityManager, $teamName ) {
        $team = $entityManager->getRepository(Team::class)->findTeamByName($teamName);
        $entityManager->remove($team);
        $entityManager->flush();
    }


    /**
     * this function update one team data
     * @param ObjectManager $entityManager
     * @param $id
     * @param Team $newTeam
     */
    public function updateTeam(ObjectManager $entityManager, $id ,Team $newTeam) {
        $team = $entityManager->getRepository(Team::class)->findTeamById($id);
        $team->setTeamName($newTeam->getTeamName());
        $entityManager->persist($team);
        $entityManager->flush();
    }


    /**
     * this function get team id from database from a select with attributes multiple
     * @param ObjectManager $entityManager
     * @param $quest
     * @return int|null
     */
    public function getTeamIdSelectMultiple(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                foreach ($qst["teamName"] as $teamName) {
                    $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($teamName);
                    return $team->getId();
                }
            }
        }
        return null;
    }


    /**
     * this function get team id from database from a select with attributes multiple=false
     * @param ObjectManager $entityManager
     * @param $quest
     * @return int|null
     */
    public function getTeamIdSelectUnique(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                return $team->getId();
            }
        }
        return null;
    }


    /**
     * this function get all team from database
     * @param ObjectManager $entityManager
     * @return mixed
     */
    public function getAllTeam(ObjectManager $entityManager){
        return $entityManager->getRepository(Team::class)->findAllTeam();
    }


    /**
     * this function get team from database depending on the given id
     * @param ObjectManager $entityManager
     * @param $id
     * @return mixed
     */
    public function getTeamById(ObjectManager $entityManager, $id){
        return $entityManager->getRepository(Team::class)->findTeamById($id);
    }


    /**
     * this function assign team and projects and save this in database
     * @param ObjectManager $entityManager
     * @param $quest
     */
    public function assignTeamProject( ObjectManager $entityManager, $quest){
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                $entityManager->persist($team);
                if (isset($qst["name"])) {
                    foreach ($qst["name"] as $projectName) {
                        $newProject= $entityManager->getRepository(Project::class)->findProjectByprojectName($projectName);
                        $newProject->addTeam($team);
                        $entityManager->persist($newProject);
                    }
                }
            }
        }
        $entityManager->flush();
    }


    /**
     * this function assign team and projects and save this in database
     * @param ObjectManager $entityManager
     * @param $quest
     */
    public function disassignProject( ObjectManager $entityManager, $quest){
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                $entityManager->persist($team);
                if (isset($qst["name"])) {
                    foreach ($qst["name"] as $projectName) {
                        $newProject= $entityManager->getRepository(Project::class)->findProjectByprojectName($projectName);
                        $newProject->removeTeam($team);
                        $entityManager->persist($newProject);
                    }
                }
            }
        }
        $entityManager->flush();
    }

}