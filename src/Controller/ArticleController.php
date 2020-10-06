<?php


namespace App\Controller;

use App\Entity\Articles;
use App\Entity\User;
use App\Form\Type\ArticleType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ArticleController extends AbstractController
{

    /**
     * @Route("/addArticle", methods={"GET"} , name="addArticle")
     */
    public function addArticle(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article = new Articles();
        $article->setDateCreation(new \DateTime("now"));
        $article->setTitre("etre ou ne pas etre");
        $article->setContenu("livre philosophique");
        $entityManager->persist($article);
        $entityManager->flush();

        $content = $this->render("Home/displayArticles.html.twig", ["articles" =>  [["titre"=>"etre ou ne pas etre"]]]);

        return new Response($content);
    }

    /**
     * @Route("newArticle", name="article_new")
     * @param Request $request : request
     */
    public function newArticle(Request $request): Response
    {
        $article = new Articles();
        //$article->setTitre("etre ou ne pas etre");
        //$article->setContenu("livre philosophique");
        $form=$this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager = $this->getDoctrine()->getManager();
            $article->setDateCreation(new \DateTime("now"));
            $entityManager->persist($article);
            $entityManager->flush();
            echo "Article ajouté avec succès<br>";
        }
        /*else{
            throw $this->createNotFoundException(
                'formulaire invalide'
            );
        }*/

        $content = $this->render("Home/newArticles.html.twig", array("formArticle"=>$form->createView()));

        return new Response($content);
    }

    /**
     * @Route("/getArticle", methods={"GET"} , name="getArticle")
     */
    public function getArticle(): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $articles= $entityManager->getRepository(Articles::class)->findArticles();
        $content = $this->render("Home/displayArticles.html.twig", ["articles" =>  $articles]);

        return new Response($content);
    }

    /**
     * @Route("/getArticle/{id}", methods={"GET"} , name="getArticle")
     * @param int $id : article id
     */
    public function getArticleById(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();

        $article= $entityManager->getRepository(Articles::class)->findArticleById($id);
        $content = $this->render("Home/displayArticles.html.twig", ["articles" =>  [$article]]);

        return new Response($content);
    }

    /**
     * @Route("/delArticle/{id}", methods={"GET"} , name="delArticle")
     * @param int $id : article id
     */
    public function delArticleById(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article= $entityManager->getRepository(Articles::class)->findArticleById($id);
        $entityManager->remove($article);
        $content = $this->render("Home/delArticle.html.twig", ["article" =>  $article]);
        $entityManager->flush();

        return new Response($content);
    }

    /**
     * @Route("/updateArticle/{id}", methods={"GET"} , name="updateArticle")
     * @param int $id : article id
     */
    public function updateArticleById(int $id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $article= $entityManager->getRepository(Articles::class)->findArticleById($id);
        $oldTitle = $article->getTitre();
        if (!$article) {
            throw $this->createNotFoundException(
                'No product found for id '.$id
            );
        }
        $article->setDateCreation(new \DateTime("now"));
        $article->setTitre("nouvel article");
        $article->setContenu("nouveau contenu");
        $entityManager->persist($article);
        $content = $this->render("Home/updateArticle.html.twig", ["oldTitle" =>  $oldTitle, "newTitle" =>  "nouvel Article"]);
        $entityManager->flush();

        return new Response($content);

    }


}