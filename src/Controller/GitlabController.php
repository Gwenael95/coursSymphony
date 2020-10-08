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
            $teamId = $gitlabServices->getTeamIdSelectUnique($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/selectTeam.html.twig", array("formTeam"=>$form->createView()));
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
        $content = $this->twig->render("Home/displayMerges.html.twig", array("merges" => $merges,
            "team"=>$gitlabServices->getTeamById($entityManager, $id)));
        return new Response($content);
    }


    /**
     * this function get all merges from our projects
     * @Route("/getMerges",  name="getMerges")
     * @param GitlabServices $gitlabServices
     * @return Response
     */
    public function getMerges( GitlabServices $gitlabServices): Response
    {
        $merges = $gitlabServices->getMerges();
        $content = $this->twig->render("Home/displayAllMerges.html.twig", array("merges" => $merges));
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
        $content = $this->twig->render("Home/displayTeam.html.twig", ["teams" =>  $teams] );
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
        //$gitlabServices->mail();

        $projects = $gitlabServices->getAllProjectInDB($this->getDoctrine()->getManager());
        $content = $this->twig->render("Home/gitlabListProjects.html.twig", array("projects" => $projects));
        return new Response($content);
    }





}