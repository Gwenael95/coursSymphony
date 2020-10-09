<?php


namespace App\Services;
use Twig\Environment;

class SendMailServices
{

    private $mailer;
    private $twig;
    private $gitlabServices;

    public function __construct(\Swift_Mailer $mailer, Environment $twig, GitlabServices $gitlabServices)
    {
        $this->mailer=$mailer;
        $this->twig=$twig;
        $this->gitlabServices = $gitlabServices;
    }


    /**
     * this function send a mail with Swift mailer, containing all merges details
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function mailSwift() {
        $mergesDetailed=$this->gitlabServices->getAllMergesDetails();
        $message = (new \Swift_Message('Merge Request'))
            ->setFrom('gwenael.mw@gmail.com')
            ->setTo('gwenael.mw@gmail.com')
            ->setBody(
                $this->twig->render(
                // templates/emails/sendMail.twig
                    'emails/sendMail.twig',
                    ['name' => "gwen", "mergesDetailed"=>$mergesDetailed]
                ),
                'text/html'
            )
        ;
        //echo($message);
        $this->mailer->send($message);
    }
}