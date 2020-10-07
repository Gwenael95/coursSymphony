<?php


namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Twig\Environment;



class DefaultController
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

    public function index(){
        $content = "page d'accueil";
        return new Response($content);
    }

    public function hello(string $prenom){
        $content = "Hello World. you are " . $prenom;
        return new Response($content);
    }


    /**
     * @Route ("films", name="app_route_auto_param")
     */
    public function films(){
        $content = $this->twig->render("Home/film.html.twig", ["films" => [
                ["title"=> "film 1", "content"=>"action", "img"=>"https://picsum.photos/200", "rates"=>"5"],
                ["title"=> "film 2", "content"=>"aventure", "img"=>"https://picsum.photos/199", "rates"=>"3"],
                ["title"=> "film 3", "content"=>"SF", "img"=>"https://picsum.photos/198", "rates"=>"4"],
                ["title"=> "film 4", "content"=>"horreur", "img"=>"https://picsum.photos/197", "rates"=>"1"],
                ["title"=> "film 5", "content"=>"SF", "img"=>"https://picsum.photos/196", "rates"=>"4"],
                ["title"=> "film 6", "content"=>"action", "img"=>"https://picsum.photos/195", "rates"=>"3"],
                ["title"=> "film 7", "content"=>"action", "img"=>"https://picsum.photos/194", "rates"=>"2"],
                ["title"=> "film 8", "content"=>"aventure", "img"=>"https://picsum.photos/193", "rates"=>"1"],
                ["title"=> "film 9", "content"=>"SF", "img"=>"https://picsum.photos/192", "rates"=>"1"],
                ["title"=> "film 10", "content"=>"action", "img"=>"https://picsum.photos/191", "rates"=>"5"],
                ["title"=> "film 11", "content"=>"horreur", "img"=>"https://picsum.photos/190", "rates"=>"4"],

        ]
        ]);
        return new Response($content);
    }

    /**
     * @Route ("exo2/{string1}", name="exo2")
     * @param Request $request : request object
     * @return Response
     */
    public function exo2(Request $request){
        $content = "ip = " . $request->getClientIp() . " arg1 : " . $request->get('string1') . " - cookie : " .  $request->cookies->get("theme") . " - method : " . $request->getMethod();;
        //$request->getQueryString();
        $response=  new Response(json_encode($content));
        $response -> headers ->set('Content-type', 'application/json');
        //var_dump($response->headers->getCookies());

        return $response;
    }
}