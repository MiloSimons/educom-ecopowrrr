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

use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        $spreadsheet1Data = json_encode($this->generateSpreadsheet1($year));
        $io->success($spreadsheet1Data);
        return Command::SUCCESS;
    }

    // An overview of all clients with their total yearly turnover per client and total bought KwH during that period
    private function generateSpreadsheet1($year) {
        $data = $this->cs->getSpreadsheet1Info($year);
        $headers = ["firstName", "lastName", "age", "gender", "totalTurnover", "totalSurplus"];
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($headers, null, 'A1');
        $sheet->fromArray($data, null, 'A2');
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save("test.xlsx");
        return($data);
    }

    // An overview of the total turnover of the current year with a prognosis based on results from the pas (trendline)
    private function generateSpreadsheet2() {
        
    }

    // An overview of the total turnover, total yield and total surplus per municipality
    private function generateSpreadsheet3() {
        
    }
}
