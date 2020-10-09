<?php


namespace App\Controller;

use App\Entity\Team;
use App\Form\Type\TeamSelectUniqueType;
use App\Services\GitlabServices;
use App\Services\TeamServices;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
    private $gitlabServices;
    private $teamServices;


    /**
     * GitlabController constructor.
     * @param Environment $twig
     * @param GitlabServices $gitlabServices
     * @param TeamServices $teamServices
     */
    public function __construct(Environment $twig, GitlabServices $gitlabServices, TeamServices $teamServices)
    {
        $this->twig = $twig;
        $this->gitlabServices=$gitlabServices;
        $this->teamServices=$teamServices;
    }


    /**
     * this function get all merges from our projects
     * @Route("/getMerges",  name="getMerges")
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getMerges(): Response
    {
        //$gitlabServices->getAllMemberFromProject(21522457);
        $merges = $this->gitlabServices->getAllMergesDetails();
        $content = $this->twig->render("Home/displayAllMerges.html.twig", array("merges" => $merges));
        return new Response($content);
    }


    /**
     * this function will select the team to display their merge requests,
     * redirect to getMergesByTeam/id with team id
     * @Route("/selectTeamMerges",  name="selectTeamMerges")
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function selectTeamMerges(Request $request): Response
    {
        $form=$this->createForm(TeamSelectUniqueType::class/*, $team*/);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $this->teamServices->getTeamIdSelectUnique($request->request->all());
            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/selectTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function get all merges depending of the selected team (in 'selectTeamMerges' page)
     * @Route("/getMergesByTeam/{id}",  name="getMergesByTeam")
     * @param int $id
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getMergesByTeam(int $id): Response
    {
        $merges = $this->gitlabServices->getMergesFromTeam( $id);
        $content = $this->twig->render("Home/displayAllMerges.html.twig", array("merges" => $merges,
            "team"=>$this->teamServices->getTeamById($id)));
        return new Response($content);
    }


}