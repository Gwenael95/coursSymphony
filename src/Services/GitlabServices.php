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

    public function addTeam(ObjectManager $entityManager, $team ) {
        $entityManager->persist($team);
        $entityManager->flush();
    }


    public function delTeam(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["name"])) {
                foreach ($qst["name"] as $teamName) {
                    $team = $entityManager->getRepository(Team::class)->findTeamByName($teamName);
                    $entityManager->remove($team);
                }
            }
        }
        $entityManager->flush();
    }

    public function redirectToUpdate(ObjectManager $entityManager, $quest ) {
        foreach($quest as $prop=>$qst){
            if (isset($qst["name"])) {
                foreach ($qst["name"] as $teamName) {
                    $team = $entityManager->getRepository(Team::class)->findTeamByName($teamName);
                    return $team->getId();
                }
            }
        }
        return null;
    }

    public function updateTeam(ObjectManager $entityManager, $id ,Team $newTeam) {
        $team = $entityManager->getRepository(Team::class)->findTeamById($id);
        $team->setName($newTeam->getName());
        $entityManager->persist($team);
        $entityManager->flush();
    }

    public function delTeamByName(ObjectManager $entityManager, $teamName ) {
        $team = $entityManager->getRepository(Team::class)->findTeamByName($teamName);
        $entityManager->remove($team);
        $entityManager->flush();
    }

    public function getAllTeam(ObjectManager $entityManager){
        return $entityManager->getRepository(Team::class)->findAllTeam();
    }

    private function getClient(){
        return $client = $this->client->authenticate('HNtbdHhikjxvHZqzeN-4', Client::AUTH_HTTP_TOKEN);
    }



    public function getAllProject(){
        $client = $this->getClient();
        return $client->projects()->all(["owned" => true,]);
    }

    public function getAllProjectsId(){
        $projects = $this->getAllProject();
        $projectsId = [];
        foreach ($projects as $row){
            array_push($projectsId, $row["id"]);
        }
        return $projectsId;
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
            if ($merge["merge_status"]==="can_be_merged") {
                array_push($array, ["status" => $merge["merge_status"], "author" => $merge["author"],
                    "upvotes" => $merge["upvotes"], "downvotes" => $merge["downvotes"], "id"=>$merge["project_id"]]);
            }
        }
        /*
        var_dump($merges);
        echo "<br><br>merges simple = ";
        var_dump($array);
*/
        return $array;
    }

    public function assignTeamProject(string $teamName, ObjectManager $entityManager, $merges){
        $project = new Project();
        $project->setProjectId($merges[0]["id"]);
        $project->setName("test");

        $team = new Team();
        $team->setName("testTeam");
        $project->addTeam($team);
        $entityManager->persist($project);
        $entityManager->persist($team);

        $entityManager->flush();
        //return $entityManager->getRepository(Project::class)->findProjectFromTeam($teamName);
    }

    public function getMergesFromTeam( ObjectManager $entityManager, string $teamName){
        return $entityManager->getRepository(Project::class)->findProjectFromTeam($teamName);
    }
}