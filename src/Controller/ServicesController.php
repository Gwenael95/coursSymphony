<?php


namespace App\Controller;


use App\Services\CalculService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class ServicesController extends AbstractController
{
    /**
     * @var CalculService
     */
    private $calculateService;

    /**
     * ServicesController constructor.
     * @param CalculService $calculateService
     */
    public function __construct(CalculService $calculateService)
    {
        $this->calculateService= $calculateService;
    }

    public function calc(int $nb1, int $nb2){
        $content = $this->render("Home/calc.html.twig", ["result" => $this->calculateService->calcul($nb1, $nb2)]);
        return new Response($content);

        //return new Response($this->calculateService->calcul($nb1, $nb2));
    }
}