<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

use App\Service\ClientService;

#[AsCommand(
    name: 'getRapport',
    description: 'Generates 3 spreadsheets rapporting different on client+device information',
)]
class GetRapportCommand extends Command
{
    private $cs;

    public function __construct(ClientService $cs)
    {
        parent::__construct();
        $this->cs = $cs;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $year = $io->ask('Please enter from which year you want a report:');
        $test= json_encode($this->generateSpreadsheet1($year));
        //dd("test");
        $io->success($test);
        return Command::SUCCESS;
    }

    // An overview of all clients with their total yearly turnover per client and total bought KwH during that period
    private function generateSpreadsheet1($year) {
        return($this->cs->getSpreadsheet1Info($year));
    }

    // An overview of the total turnover of the current year with a prognosis based on results from the pas (trendline)
    private function generateSpreadsheet2() {
        
    }

    // An overview of the total turnover, total yield and total surplus per municipality
    private function generateSpreadsheet3() {
        
    }
}
