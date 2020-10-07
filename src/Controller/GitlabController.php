<?php


namespace App\Controller;

use App\Entity\Articles;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GitlabController
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
     * @Route("/setTeam", methods={"GET"} , name="setTeam")
     */
    public function setTeam(): Response
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
}