<?php


namespace App\Controller;

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

    /**
     * GitlabController constructor.
     * @param Environment $twig
     */
    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }


    /**
     * this function get all merges from our projects
     * @Route("/getMerges",  name="getMerges")
     * @param GitlabServices $gitlabServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getMerges( GitlabServices $gitlabServices): Response
    {
        //$gitlabServices->getAllMemberFromProject(21522457);
        $merges = $gitlabServices->getAllMergesDetails();
        $content = $this->twig->render("Home/displayAllMerges.html.twig", array("merges" => $merges));
        return new Response($content);
    }


    /**
     * this function will select the team to display their merge requests,
     * redirect to getMergesByTeam/id with team id
     * @Route("/selectTeamMerges",  name="selectTeamMerges")
     * @param Request $request
     * @param TeamServices $teamServices
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function selectTeamMerges(Request $request, TeamServices $teamServices): Response
    {
        $form=$this->createForm(TeamSelectUniqueType::class/*, $team*/);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $teamId = $teamServices->getTeamIdSelectUnique($this->getDoctrine()->getManager(), $request->request->all());
            return $this->redirectToRoute('getMergesByTeam', ["id"=>$teamId]);
        }
        $content = $this->twig->render("Home/selectTeam.html.twig", array("formTeam"=>$form->createView()));
        return new Response($content);
    }

    /**
     * this function get all merges depending of the selected team (in 'selectTeamMerges' page)
     * @Route("/getMergesByTeam/{id}",  name="getMergesByTeam")
     * @param GitlabServices $gitlabServices
     * @param TeamServices $teamServices
     * @param int $id
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function getMergesByTeam( GitlabServices $gitlabServices, TeamServices $teamServices, int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $merges = $gitlabServices->getMergesFromTeam($entityManager, $id);
        $content = $this->twig->render("Home/displayAllMerges.html.twig", array("merges" => $merges,
            "team"=>$teamServices->getTeamById($entityManager, $id)));
        return new Response($content);
    }


}