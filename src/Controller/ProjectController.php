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
    private $teamServices;
    private $gitServices;

    /**
     * TeamController constructor.
     * @param Environment $twig
     * @param TeamServices $teamServices
     * @param GitlabServices $gitServices
     */
    public function __construct(Environment $twig, TeamServices $teamServices, GitlabServices $gitServices)
    {
        $this->twig = $twig;
        $this->teamServices=$teamServices;
        $this->gitServices=$gitServices;
    }


    /**
     * this function list only all projects in database
     * @Route("/getProject",  name="getProject")
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getProject(): Response
    {
        $projects = $this->gitServices->getAllProjectInDB();
        $content = $this->twig->render("Home/listProjects.html.twig", array("projects" => $projects));
        return new Response($content);
    }


    /**
     * this function allow to assign project to a team
     * @Route("/assignProject",  name="assignProject")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function assignProject(Request $request): Response
    {
        $this->gitServices->updateProjectInDb();
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->teamServices->assignTeamProject($request->request->all());
            $teamId = $this->teamServices->getTeamIdSelectUnique($request->request->all());
            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/assignTeamProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function allow to disassign project to a team
     * @Route("/disassignProject",  name="disassignProject")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function disassignProject(Request $request ): Response
    {
        $this->gitServices->updateProjectInDb();
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->teamServices->disassignProject($request->request->all());
            $teamId = $this->teamServices->getTeamIdSelectUnique($request->request->all());

            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/disassignProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }
}