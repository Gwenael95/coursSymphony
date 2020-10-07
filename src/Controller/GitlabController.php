<?php


namespace App\Controller;

use App\Entity\Team;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Form\Type\TeamType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GitlabController  extends AbstractController
{
    /**
     * @Route("/mergeRequest", methods={"GET"} , name="mergeRequest")
     */
    public function mergeRequest(): Response
    {
        /*$entityManager = $this->getDoctrine()->getManager();
        $article = new Articles();
        $article->setDateCreation(new \DateTime("now"));
        $article->setTitre("etre ou ne pas etre");
        $article->setContenu("livre philosophique");
        $entityManager->persist($article);
        $entityManager->flush();

        $content = $this->render("Home/displayArticles.html.twig", ["articles" =>  [["titre"=>"etre ou ne pas etre"]]]);
        */
        // Token authentication
        /*$client = new Gitlab\Client();
        $client->authenticate('HNtbdHhikjxvHZqzeN-4', Gitlab\Client::AUTH_HTTP_TOKEN);

        // OAuth2 authentication
        $client = new Gitlab\Client();
        $client->authenticate('HNtbdHhikjxvHZqzeN-4', Gitlab\Client::AUTH_OAUTH_TOKEN);

        // An example API call
        $project = $client->projects()->create('My Project', [
            'description' => 'This is a project',
            'issues_enabled' => false,
        ]);
        var_dump($project);

        return new Response("a");*/
    }


    /**
     * @Route("/mergeRequestList", methods={"GET"} , name="mergeRequestList")
     */
    public function mergeRequestList(): Response
    {
        /*$entityManager = $this->getDoctrine()->getManager();
        $article = new Articles();
        $article->setDateCreation(new \DateTime("now"));
        $article->setTitre("etre ou ne pas etre");
        $article->setContenu("livre philosophique");
        $entityManager->persist($article);
        $entityManager->flush();

        $content = $this->render("Home/displayArticles.html.twig", ["articles" =>  [["titre"=>"etre ou ne pas etre"]]]);

        //return new Response($content);*/

    }


    /**
     * @Route("/setTeam",  name="setTeam")
     * @param Request $request
     * @return Response
     */
    public function setTeam(Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $team = new Team();
        $form=$this->createForm(TeamType::class, $team);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($team);
            $entityManager->flush();
            echo "equipe selectionné<br>";
        }

        $content = $this->render("Home/gitlabSetTeam.html.twig", array("formTeam"=>$form->createView()));

        return new Response($content);

    }
}