<?php


namespace App\Controller;


use App\Entity\Project;
use App\Entity\Team;
use App\Form\Type\TeamProjectAssignType;
use App\Services\GitlabServices;
use App\Services\TeamServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


/**
 * @Route("/")
 */
class ProjectController  extends AbstractController
{

    /**
     * @var Environment
     */
    private $twig;

    /**
     * TeamController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }


    /**
     * this function list only all projects in database
     * @Route("/getProject",  name="getProject")
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getProject( GitlabServices $gitlabServices): Response
    {
        $projects = $gitlabServices->getAllProjectInDB($this->getDoctrine()->getManager());
        $content = $this->twig->render("Home/listProjects.html.twig", array("projects" => $projects));
        return new Response($content);
    }


    /**
     * this function allow to assign project to a team
     * @Route("/assignProject",  name="assignProject")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @param TeamServices $teamServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function assignProject(Request $request, GitlabServices $gitlabServices, TeamServices $teamServices): Response
    {
        $gitlabServices->updateProjectInDb($this->getDoctrine()->getManager());
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamServices->assignTeamProject($this->getDoctrine()->getManager(),  $request->request->all());
            $teamId = $teamServices->getTeamIdSelectUnique($this->getDoctrine()->getManager(), $request->request->all());

            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);

        }
        $content = $this->twig->render("Home/assignTeamProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function allow to disassign project to a team
     * @Route("/disassignProject",  name="disassignProject")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function disassignProject(Request $request, GitlabServices $gitlabServices, TeamServices $teamServices): Response
    {
        $gitlabServices->updateProjectInDb($this->getDoctrine()->getManager());
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamServices->disassignProject($this->getDoctrine()->getManager(),  $request->request->all());
            $teamId = $teamServices->getTeamIdSelectUnique($this->getDoctrine()->getManager(), $request->request->all());

            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/disassignProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }



}