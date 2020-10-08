<?php


namespace App\Services;


use App\Entity\Project;
use App\Entity\Team;
use Doctrine\Persistence\ObjectManager;
use Gitlab\Client;
use Twig\Environment;

class GitlabServices
{
    private $client;
    private $mailer;
    private $twig;

    public function __construct(Client $client, \Swift_Mailer $mailer, Environment $twig)
    {
        $this->client = $client;
        $this->mailer=$mailer;
        $this->twig=$twig;
    }


    private function getClient(){
        return $client = $this->client->authenticate('HNtbdHhikjxvHZqzeN-4', Client::AUTH_HTTP_TOKEN);
    }


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
     * this function get all project from Gitlab API
     * @return mixed
     */
    public function getAllProject(){
        $client = $this->getClient();
        return $client->projects()->all(["owned" => true,"simple"=>true]);
    }

    /**
     * this function get all project saved in database (useful if we add team's image in DB)
     * @param ObjectManager $entityManager
     * @return mixed
     */
    public function getAllProjectInDB(ObjectManager $entityManager){
        return $entityManager->getRepository(Project::class)->findAllProject();;
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
    public function getMerges(){
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
     * this function send a mail with Swift mailer, containing all merges details
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function mailSwift() {
        $mergesDetailed=$this->getAllMergesDetails();
        $message = (new \Swift_Message('Merge Request'))
            ->setFrom('gwenael.mw@gmail.com')
            ->setTo('gwenael.mw@gmail.com')
            ->setBody(
                $this->twig->render(
                // templates/emails/sendMail.twig
                    'emails/sendMail.twig',
                    ['name' => "gwen", "mergesDetailed"=>$mergesDetailed]
                ),
                'text/html'
            )
        ;

        $this->mailer->send($message);
    }


    /**
     * this function get all merges for a team depending on its related projects
     * @param ObjectManager $entityManager
     * @param int $id
     * @return array
     */
    public function getMergesFromTeam( ObjectManager $entityManager, int $id){
        $team = $entityManager->getRepository(Team::class)->findTeamById($id);
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