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
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Axis;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\TrendLine;

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

        //$year = $io->ask('Please enter from which year you want a report:');
        //$spreadsheet1Data = json_encode($this->generateSpreadsheet1($year));

        $currentYear = date("Y");
        $spreadsheet2Data = json_encode($this->generateSpreadsheet2($currentYear));
        //$io->success($spreadsheet1Data);
        $io->success($spreadsheet2Data);
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
        $writer->save("test.xlsx"); //rename!
        return($data);
    }

    // An overview of the total turnover of the current year with a prognosis based on results from the pas (trendline)
    private function generateSpreadsheet2($currentYear) {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        
        $data = $this->cs->getSpreadsheet2Info($currentYear);
        //dd($data);
        //$trendlineFormula = $this->cs->calculateTrendline($data);
        //dd($trendlineFormula);

        $headers = [array_keys($data)];    
        
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle("Sheet1");
        //$sheet->fromArray($headers, null, 'A1');
        //$sheet->fromArray($data, null, 'A2');
        $sheet->setCellValue('A1', 'Date');
        $sheet->setCellValue('B1', 'Overturn');

        $row = 2;
        
        foreach ($data as $date => $overturn) {

            $sheet->setCellValue("A$row", $date);
            $sheet->getStyle("A$row")->getNumberFormat()->setFormatCode("MM-YYYY");
            $sheet->setCellValue("B$row", $overturn);
            $row++;
        }
        //dump($row);
        $value = $row-2;
        //dump($value);
        // Set chart data ranges (use the data for the x and y values)
        $labels = new DataSeriesValues('String', 'Sheet1!$A$2:$A$13', null, $value);
        $values = new DataSeriesValues('Number', 'Sheet1!$B$2:$B$13', null, $value);
        $dsl = [
                new DataSeriesValues('String', 'Sheet1!$A$1', null, 1),
                new DataSeriesValues('String', 'Sheet1!$B$1', null, 1)
            ];
        //dd($dsl);
        //dd($labels, $values);
        // Create a DataSeries object for the scatter plot
        $series = new DataSeries(
            DataSeries::TYPE_SCATTERCHART,
            null,//DataSeries::GROUPING_CLUSTERED, 
            range(0, count([$values])-1), 
            $dsl, 
            [$labels], 
            [$values]
        );

        // Create a plot area and a chart
        $plotArea = new PlotArea(null, [$series]);
        $chart = new Chart(
            'trendlineChart', // chart name
            new Title('Total overturn per month'),
            new Legend(), 
            $plotArea, 
            //new Axis('Date Axis', Axis::POSITION_BOTTOM), 
            //new Axis('Value Axis', Axis::POSITION_LEFT)
        );

        // Position the chart on the spreadsheet
        $chart->setTopLeftPosition('D2');
        $chart->setBottomRightPosition('L15');

        // Add the trendline (linear) with forecast
        
        //$trendline = new Trendline();
        //$trendline->setType(TrendLine::TYPE_LINEAR);  // Linear trendline
        //$trendline->setForecast(5);                   // Prognosis (forecast 5 periods ahead)
        //$series->addTrendLine($trendline);
        
        // Add the chart to the worksheet
        $sheet->addChart($chart);


        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        //$writer->setPreCalculateFormulas(false);
        $writer->setIncludeCharts(true);
        $writer->save("test2.xlsx");
        return($data);
    }

    // An overview of the total turnover, total yield and total surplus per municipality
    private function generateSpreadsheet3() {
        
    }
}
