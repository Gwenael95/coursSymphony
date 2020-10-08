<?php


namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Form\Type\TeamProjectAssignType;
use App\Form\Type\TeamSelectMultipleType;
use App\Form\Type\TeamSelectUniqueType;
use App\Services\GitlabServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\TeamType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
     * this function list only all projects in database
     * @Route("/getProject",  name="getProject")
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function getProject( GitlabServices $gitlabServices): Response
    {
        $projects = $gitlabServices->getAllProjectInDB($this->getDoctrine()->getManager());
        $content = $this->render("Home/gitlabListProjects.html.twig", array("projects" => $projects));
        return new Response($content);
    }


    /**
     * this function allow to assign project to a team
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

        $gitlabServices->updateProject($this->getDoctrine()->getManager());
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
     * this function will select the team to display their merge requests,
     * redirect to getMergesByTeam/id with team id
     * @Route("/selectTeam",  name="selectTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function selectTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $form=$this->createForm(TeamSelectUniqueType::class/*, $team*/);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $gitlabServices->redirectToSelect($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function get all merges depending of the selected team (in selectTeam page)
     * @Route("/getMergesByTeam/{id}",  name="getMergesByTeam")
     * @param GitlabServices $gitlabServices
     * @param int $id
     * @return Response
     */
    public function getMergesByTeam( GitlabServices $gitlabServices, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $merges = $gitlabServices->getMergesFromTeam($entityManager, $id);
        $content = $this->render("Home/displayMerges.html.twig", array("merges" => $merges));
        return new Response($content);
    }
         return $this->render("Home/displayMerges.html.twig", array("merges" => $merges));







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
     * this function will select the team to update, redirect to updateTeam with team id
     * to update it
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function setTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectMultipleType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $gitlabServices->redirectToUpdate($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('updateTeam', ["id"=>$teamId]);
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function is used to update a team after having select it (in setTeam)
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
     * this function allow to delete team thanks to a form, getting all team in database to
     * know which one we could delete
     * @Route("/delTeam", name="delTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function delTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectMultipleType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->delTeam($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('delTeam');
        }
        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function display a team list from database
     * @Route("/getTeam", methods={"GET"} , name="getTeam")
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function getTeam(GitlabServices $gitlabServices): Response
    {
        $teams = $gitlabServices->getAllTeam($this->getDoctrine()->getManager());
        $content = $this->render("Home/displayTeam.html.twig", ["teams" =>  $teams] );
        return new Response($content);
    }
}