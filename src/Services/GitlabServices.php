<?php


namespace App\Services;

use App\Entity\Project;
use App\Entity\Team;
use Doctrine\ORM\EntityManagerInterface;
use Gitlab\Client;

class GitlabServices
{
    private $client;
    private $em;

    public function __construct(Client $client, EntityManagerInterface $em)
    {
        $this->client = $client->authenticate('HNtbdHhikjxvHZqzeN-4', Client::AUTH_HTTP_TOKEN);
        $this->em=$em;
    }


    /**
     * this function get all project from Gitlab API
     * @return mixed
     */
    public function getAllProject(){
        return $this->client->projects()->all(["owned" => true,"simple"=>true]);
    }


    /**
     * this function get all project saved in database (useful if we add team's image in DB)
     * @return mixed
     */
    public function getAllProjectInDB(){
        return $this->em->getRepository(Project::class)->findAllProject();;
    }


    /**
     * this function get only all projects id from API
     * @return array
     */
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
     */
    public function updateProjectInDb() {
        $gitProjects = $this->getAllProject();
        foreach ($gitProjects as $project){
            $newProject= $this->em->getRepository(Project::class)->findOneProjectByProjectId($project["id"]);
            if ($newProject==null){
                $newProject = new Project();
            }
            $newProject->setName($project["name"]);
            $newProject->setProjectId($project["id"]);
            $this->em->persist($newProject);
        }
        $this->em->flush();
    }


    /**
     * this function get all project members from API, thanks to a project id
     * @param int $projectId
     * @return mixed
     */
    public function getAllMemberFromProject(int $projectId){
        //21522457
        $members = $this->client->projects()->allMembers( $projectId);
        return $members;
    }


    /**
     * this function get all merges from API, with all useful data (but not more)
     * @return array
     */
    private function getMerges(){
        $merges = $this->client->mergeRequests()->all();

        $array = [];
        foreach ($merges as $merge){
            if ($merge["state"]==="opened") {
                array_push($array, ["status" => $merge["merge_status"], "author" => $merge["author"],
                    "upvotes" => $merge["upvotes"], "downvotes" => $merge["downvotes"], "id"=>$merge["project_id"],
                    "target"=>$merge["target_branch"], "source"=>$merge["source_branch"], "comments" =>$merge["user_notes_count"],
                    "labels"=>$merge["labels"], "title" => $merge["title"],
                    "tag"=>($merge["milestone"] ==null ? $merge["milestone"] : $merge["milestone"]["title"])]);
            }
        }
        return $array;
    }


    /**
     * this function get all merges details, and add the project name for each one,
     * it seem to be the more efficient way to do this, some other solution take too much time
     * @return array
     */
    public function getAllMergesDetails(){
        $merges= $this->getMerges();
        $projects= $this->getAllProject();
        $mergesDetailed=[];
        foreach ($projects as $p) {
            foreach ($merges as $m){
                if ($m["id"]==$p["id"]){
                    array_push($mergesDetailed, array_merge($m, ["projectName"=>$p["name"]]));
                }
            }
        }
        return $mergesDetailed;
    }


    /**
     * this function get all merges for a team depending on its related projects
     * @param int $id
     * @return array
     */
    public function getMergesFromTeam( int $id){
        $team = $this->em->getRepository(Team::class)->findTeamById($id);
        $projects = $team->getProjects();
        $merges = $this->getAllMergesDetails();
        $teamMerges = [];
        foreach ($projects as $p) {
            foreach ($merges as $m){
                if ($m["id"]==$p->getProjectId()){
                    array_push($teamMerges, $m);
                }
            }
        }
        return $teamMerges;
    }

}