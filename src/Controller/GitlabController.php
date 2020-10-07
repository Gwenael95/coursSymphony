<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Services\GitlabServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\TeamType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Gitlab\Client;


class GitlabController  extends AbstractController
{

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
     * @Route("/assignMerges",  name="assignMerges")
     * @param GitlabServices $gitlabServices
     * @param string $teamName
     * @return Response
     */
    public function assignMerges( GitlabServices $gitlabServices): Response
    {
        $merges = $gitlabServices->getMerges();
        $entityManager = $this->getDoctrine()->getManager();

        $gitlabServices->assignTeamProject("testTeam", $entityManager, $merges);

        //$merges = $gitlabServices->getMergesFromTeam($teamName, $entityManager);

        $content = $this->render("Home/displayMerges.html.twig", array("merges" => $merges));

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
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function setTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $projects = $gitlabServices->getAllProject();

        //var_dump($projects);
        $projectsId = $gitlabServices->getAllProjectsId();
        var_dump($projectsId);

        $team = new Team();
        $form=$this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $gitlabServices->addTeam($entityManager, $team);
            echo "equipe selectionné<br>";
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
        $form=$this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $gitlabServices->delTeam($entityManager, $team);
            echo "equipe supprimé<br>";
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