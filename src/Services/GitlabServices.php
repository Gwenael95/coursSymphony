<?php


namespace App\Services;


use App\Entity\Project;
use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;
use Gitlab\Client;

class GitlabServices
{
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    private function getClient(){
        return $client = $this->client->authenticate('HNtbdHhikjxvHZqzeN-4', Client::AUTH_HTTP_TOKEN);
    }


    public function addTeam(ObjectManager $entityManager, Team $team ) {
        $entityManager->persist($team);
        $entityManager->flush();
    }


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
    public function delTeamByName(ObjectManager $entityManager, $teamName ) {
        $team = $entityManager->getRepository(Team::class)->findTeamByName($teamName);
        $entityManager->remove($team);
        $entityManager->flush();
    }

    public function updateTeam(ObjectManager $entityManager, $id ,Team $newTeam) {
        $team = $entityManager->getRepository(Team::class)->findTeamById($id);
        $team->setTeamName($newTeam->getTeamName());
        $entityManager->persist($team);
        $entityManager->flush();
    }


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

    public function getTeamIdSelectUnique(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["teamName"])) {
                $team = $entityManager->getRepository(Team::class)->findTeamByTeamName($qst["teamName"]);
                return $team->getId();
            }
        }
        return null;
    }

    public function getAllTeam(ObjectManager $entityManager){
        return $entityManager->getRepository(Team::class)->findAllTeam();
    }

    public function getTeamById(ObjectManager $entityManager, $id){
        return $entityManager->getRepository(Team::class)->findTeamById($id);
    }

    public function assignTeamProject( ObjectManager $entityManager,/*string $teamName,*/ $quest){
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




    public function getAllProject(){
        $client = $this->getClient();
        return $client->projects()->all(["owned" => true,]);
    }

    public function getAllProjectInDB(ObjectManager $entityManager){
        return $entityManager->getRepository(Project::class)->findAllProject();;
    }

    public function getAllProjectsId(){
        $projects = $this->getAllProject();
        $projectsId = [];
        foreach ($projects as $row){
            array_push($projectsId, $row["id"]);
        }
        return $projectsId;
    }


    /**
     * This function get all project on gitlab and save them in database
     * @param ObjectManager $entityManager
     */
    public function updateProject(ObjectManager $entityManager) {
        $gitProjects = $this->getAllProject();
        foreach ($gitProjects as $project){
            $newProject= $entityManager->getRepository(Project::class)->findOneProjectByProjectId($project["id"]);
            if ($newProject==null){
                $newProject = new Project();
            }
            $newProject->setName($project["name"]);
            $newProject->setProjectId($project["id"]);
            $entityManager->persist($newProject);
        }
        $entityManager->flush();
    }


    public function getAllMemberFromProject(int $projectId){
        //$members = $this->client->projects()->allMembers(21522457);
        $members = $this->client->projects()->allMembers( $projectId);

        ///21256897  --  21522457  --  21256865  --  21256859  --  21256854   --  21256849 --  21221266
        /// our projects
        /*var_dump($onePro);
        foreach ($onePro as $user){
            echo $user["username"];
        }*/
        return $members;
    }

    public function getMerges(){
        $merges = $this->client->mergeRequests()->all();

        $array = [];
        foreach ($merges as $merge){
            if ($merge["state"]==="opened") {
                array_push($array, ["status" => $merge["merge_status"], "author" => $merge["author"],
                    "upvotes" => $merge["upvotes"], "downvotes" => $merge["downvotes"], "id"=>$merge["project_id"],
                    "target"=>$merge["target_branch"], "source"=>$merge["source_branch"]]);
            }
        }
        return $array;
    }

    public function getMergesFromTeam( ObjectManager $entityManager, int $id){
        $team = $entityManager->getRepository(Team::class)->findTeamById($id);
        $projects = $team->getProjects();
        $merges = $this->getMerges();
        $teamMerges = [];
        foreach ($projects as $p) {
            foreach ($merges as $m){
                if ($m["id"]==$p->getProjectId()){
                    array_push($teamMerges, array_merge($m, ["projectName"=>$p->getName()]));
                    break;
                }
            }
        }
        //var_dump($teamMerges);
        return $teamMerges;
    }

}