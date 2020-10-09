<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Form\Type\CreateTeamType;
use App\Form\Type\TeamProjectAssignType;
use App\Form\Type\TeamSelectMultipleType;
use App\Services\GitlabServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

/**
 * @Route("/")
 */
class TeamController extends AbstractController
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
     * this will create a new team thanks to a form with a textField input for team name
     * @Route("/createTeam",  name="createTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(CreateTeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->addTeam($this->getDoctrine()->getManager(), $team);
        }

        $content = $this->twig->render("Home/createTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function will select the team to update, before to redirect to 'updateTeam' with team id
     * to update it
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function setTeam(Request $request, GitlabServices $gitlabServices): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectMultipleType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $gitlabServices->getTeamIdSelectMultiple($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('updateTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/setTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function is used to update a team after having select it (in setTeam)
     * @Route("/updateTeam/{id}",  name="updateTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @param int $id
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updateTeam(Request $request, GitlabServices $gitlabServices, int $id): Response
    {
        $team = new Team();
        $form=$this->createForm(CreateTeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->updateTeam($this->getDoctrine()->getManager(), $id, $team);
        }
        $content = $this->twig->render("Home/setTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function allow to delete team thanks to a form, getting all team in database to
     * know which one we could delete
     * @Route("/delTeam", name="delTeam")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
        $content = $this->twig->render("Home/delTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function allow to assign project to a team
     * @Route("/assignProject",  name="assignProject")
     * @param Request $request
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function assignProject(Request $request, GitlabServices $gitlabServices): Response
    {
        $gitlabServices->updateProject($this->getDoctrine()->getManager());
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->assignTeamProject($this->getDoctrine()->getManager(),  $request->request->all());
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
    public function disassignProject(Request $request, GitlabServices $gitlabServices): Response
    {
        $gitlabServices->updateProject($this->getDoctrine()->getManager());
        $team = new Team();
        $project = new Project();
        $form=$this->createForm(TeamProjectAssignType::class, [$team, $project]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $gitlabServices->disassignProject($this->getDoctrine()->getManager(),  $request->request->all());
        }
        $content = $this->twig->render("Home/disassignProject.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }
}
