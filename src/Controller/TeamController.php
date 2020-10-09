<?php

namespace App\Controller;

use App\Entity\Team;
use App\Form\Type\CreateTeamType;
use App\Form\Type\TeamSelectMultipleType;
use App\Services\TeamServices;
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
    private $teamServices;

    /**
     * TeamController constructor.
     * @param Environment $twig
     * @param TeamServices $teamServices
     */
    public function __construct(Environment $twig, TeamServices $teamServices)
    {
        $this->twig = $twig;
        $this->teamServices=$teamServices;
    }


    /**
     * this will create a new team thanks to a form with a textField input for team name
     * @Route("/createTeam",  name="createTeam")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function createTeam(Request $request): Response
    {
        $team = new Team();
        $form=$this->createForm(CreateTeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->teamServices->addTeam($team);
            return $this->redirectToRoute('getTeam');
        }

        $content = $this->twig->render("Home/createTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function will select the team to update, before to redirect to 'updateTeam' with team id
     * to update it
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function setTeam(Request $request): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectMultipleType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $this->teamServices->getTeamIdSelectMultiple($request->request->all());
            return $this->redirectToRoute('updateTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/setTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function is used to update a team after having select it (in setTeam)
     * @Route("/updateTeam/{id}",  name="updateTeam")
     * @param Request $request
     * @param int $id
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function updateTeam(Request $request, int $id): Response
    {
        $team = new Team();
        $form=$this->createForm(CreateTeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->teamServices->updateTeam($id, $team);
            return $this->redirectToRoute('getTeam');

        }
        $content = $this->twig->render("Home/setTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }


    /**
     * this function allow to delete team thanks to a form, getting all team in database to
     * know which one we could delete
     * @Route("/delTeam", name="delTeam")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function delTeam(Request $request): Response
    {
        $team = new Team();
        $form=$this->createForm(TeamSelectMultipleType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $this->teamServices->delTeam($request->request->all());
            return $this->redirectToRoute('delTeam');
        }
        $content = $this->twig->render("Home/delTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function display a team list from database
     * @Route("/getTeam", methods={"GET"} , name="getTeam")
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getTeam(): Response
    {
        $teams = $this->teamServices->getAllTeam();
        $content = $this->twig->render("Home/displayTeam.html.twig", ["teams" =>  $teams] );
        return new Response($content);
    }


}
