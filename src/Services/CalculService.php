<?php


namespace App\Services;
use Psr\Log\LoggerInterface;

class CalculService
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * CalculService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function calcul(int $nb1,int $nb2 ):int {
        $result = $nb1+$nb2;
        $this->logger->info( "Addition : " . $nb1 . " + " . $nb2 . "= " . $result);
        return $result;
    }
}