<?php

namespace App\Command;

use App\Services\GitlabServices;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Twig\Environment;

class SendMailCommand extends Command
{
    protected static $defaultName = 'app:sendMail';

    //private $em;
    private $gitlabServices;

    public function __construct(/*ObjectManager $em,*/ GitlabServices $gitlabServices)
    {
        //$this->em = $em;
        $this->gitlabServices = $gitlabServices;
        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $arg1 = $input->getArgument('arg1');

        $this->gitlabServices->mailSwift();


        }

        if ($arg1) {
            $io->note(sprintf('You passed an argument: %s', $arg1));
        }

        if ($input->getOption('option1')) {
            // ...
        }

        $io->success('mail sended successfully.');

        return Command::SUCCESS;
    }
}
