<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Form\Type\TeamProjectAssignType;
use App\Form\Type\TeamSelectType;
use App\Services\GitlabServices;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\TeamType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gitlab\Client;
use Twig\Environment;


class GitlabController  extends AbstractController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * DefaultController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }
    /**
     * @Route("/getMerges",  name="getMerges")
     * @param GitlabServices $gitlabServices
     * @param string $teamName
     * @return Response
     */
    public function getMerges( GitlabServices $gitlabServices): Response
    {
        $merges = $gitlabServices->getMerges();
        $content = $this->render("Home/displayMerges.html.twig", array("merges" => $merges));
        return new Response($content);
    }



    /**
     * @Route("/getProject",  name="getProject")
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function getProject( GitlabServices $gitlabServices): Response
    {
        $projects = $gitlabServices->getAllProject();

        //var_dump($projects);
        $projectsId = $gitlabServices->getAllProjectsId();
        var_dump($projectsId);
        echo "<br><br>";
         $gitlabServices->getMerges();

        $content = $this->render("Home/testGitlab.html.twig", array("test" => "test"));

        return new Response($content);
    }


    /**
     * @Route("/assignProject",  name="assignProject")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function assignProject(Request $request, GitlabServices $gitlabServices): Response
    {
        //$merges = $gitlabServices->getMerges();
        //$gitlabServices->assignTeamProject("testTeam", $entityManager, $merges);
        //$merges = $gitlabServices->getMergesFromTeam($teamName, $entityManager);
        //$content = $this->render("Home/displayMerges.html.twig", array("merges" => $merges));

        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->assignTeamProject($this->getDoctrine()->getManager(),  $request->request->all());
            echo "assignation des projet réussi<br>";
        }
        $content = $this->render("Home/gitlabAssignProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }



    /**
     * @Route("/getMergesByTeam/{teamName}",  name="getMergesByTeam")
     * @param GitlabServices $gitlabServices
     * @param string $teamName
     * @return Response
     */
    public function getMergesByTeam( GitlabServices $gitlabServices, string $teamName): Response
    {
        //donner tous les merges en rapport a une team, dans la bdd du coup
        $entityManager = $this->getDoctrine()->getManager();
        $merges = $gitlabServices->getMergesFromTeam($entityManager, $teamName);

        $content = $this->render("Home/displayMerges.html.twig", array("merges" => $merges));

        return new Response($content);
    }







    /**
     * this will create a new team thanks to a form with a textField input for team name
     * @Route("/createTeam",  name="createTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function createTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->addTeam($this->getDoctrine()->getManager(), $team);
            echo "equipe ajouté<br>";
        }

        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));

        return new Response($content);
    }


    /**
     * this will select the team to update
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function setTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $gitlabServices->redirectToUpdate($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('updateTeam', ["id"=>$teamId]);
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * @Route("/updateTeam/{id}",  name="updateTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @param int $id
     * @return Response
     */
    public function updateTeam(Request $request, GitlabServices $gitlabServices, int $id): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->updateTeam($this->getDoctrine()->getManager(), $id, $team);
            echo "équipe mise à jour<br>";
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * @Route("/delTeam", name="delTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function delTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->delTeam($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('delTeam');
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * @Route("/getTeam", methods={"GET"} , name="getTeam")
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function getTeam(GitlabServices $gitlabServices): Response
    {
        $members = $gitlabServices->getAllMemberFromProject(21256859);
        $entityManager = $this->getDoctrine()->getManager();
        $teams = $gitlabServices->getAllTeam($entityManager);
        $content = $this->render("Home/displayTeam.html.twig", ["teams" =>  $teams , "members"=>$members]);

        return new Response($content);
    }
}