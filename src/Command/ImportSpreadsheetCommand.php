<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use App\Service\ClientService;
use App\Service\DeviceService;
use App\Service\PriceService;
use App\Service\DeviceMetricsService;
use App\Service\MonthlyUsedService;

#[AsCommand(
    name: 'app:import-spreadsheet',
    description: 'Import excel spreadsheet',
)]
class ImportSpreadsheetCommand extends Command
{
    private $cs;
    private $ds;
    private $ps;
    private $dms;
    private $mus;

    public function __construct(ClientService $cs, DeviceService $ds, PriceService $ps, DeviceMetricsService $dms, MonthlyUsedService $mus)
    {
        parent::__construct();
        $this->cs = $cs;
        $this->ds = $ds;
        $this->ps = $ps;
        $this->dms = $dms;
        $this->mus = $mus;
    }

    protected function configure(): void
    {
        $this
            ->setHelp('This command allows you to import a spreadsheet')
            ->addArgument('file', InputArgument::REQUIRED, 'Spreadsheet')
        ;   
    }    
    
    protected function execute(InputInterface $input, OutputInterface $output): int
    {   
        $inputFileName = $input->getArgument('file');
        $spreadsheet = IOFactory::load($inputFileName);
        $io = new SymfonyStyle($input, $output);

        $clientSheet        = $spreadsheet->getSheetByName("Client");
        $deviceSheet        = $spreadsheet->getSheetByName("Device");
        $priceSheet         = $spreadsheet->getSheetByName("Price");
        $deviceMetricsSheet = $spreadsheet->getSheetByName("DeviceMetrics");
        $monthlyUsedSheet   = $spreadsheet->getSheetByName("MonthlyUsed");

        $clientData        = $clientSheet->toArray("", true, true);
        $deviceData        = $deviceSheet->toArray("", true, true);
        $priceData         = $priceSheet->toArray("", true, true);
        $deviceMetricsData = $deviceMetricsSheet->toArray("", true, true);
        $monthlyUsedData   = $monthlyUsedSheet->toArray("", true, true);        
        
        $this->addClientData($clientData);
        $this->addDeviceData($deviceData);
        $this->addPriceData($priceData);
        $this->addDeviceMetricsData($deviceMetricsData);
        $this->addMonthlyUsedData($monthlyUsedData);       

        return Command::SUCCESS;
    }
    
    private function addClientData($clientData) {
        for($i = 1; $i<count($clientData); $i++) {
            $clientAdvisorId   = $clientData[$i][1];
            $bankAccountNumber = $clientData[$i][2];
            $zipCode           = $clientData[$i][3];
            $houseNumber       = $clientData[$i][4];
            $firstName         = $clientData[$i][5];
            $lastName          = $clientData[$i][6];
            $age               = $clientData[$i][7];
            $gender            = $clientData[$i][8];
            $type              = $clientData[$i][9];
            
            $params = [
                       "clientAdvisorId" => $clientAdvisorId,
                       "bankAccountNumber" => $bankAccountNumber,
                       "zipCode" => $zipCode,
                       "houseNumber" => $houseNumber,
                       "firstName" => $firstName,
                       "lastName" => $lastName, 
                       "age" => $age,
                       "gender" => $gender,
                       "type" => $type
                      ];
            $this->cs->addClient($params);            
        }
    }

    private function addDeviceData($deviceData) {
        for($i = 1; $i<count($deviceData); $i++) {
            $overallDeviceId = $deviceData[$i][1];
            $serialNumber    = $deviceData[$i][2];
            $type            = $deviceData[$i][3];
            
            $params = [
                       "overallDeviceId" => (int)$overallDeviceId,
                       "serialNumber" => $serialNumber,
                       "type" => $type
                      ];
            $this->ds->addDevice($params);            
        }
    }

    private function addPriceData($priceData) {
        for($i = 1; $i<count($priceData); $i++) {
            $buyInPrice = $priceData[$i][1];
            $startDate  = $priceData[$i][2];
            $endDate    = $priceData[$i][3];
            
            $params = [
                       "buyInPrice" => (float)$buyInPrice,
                       "startDate" => date_create_from_format("Y-m-d", $startDate),
                       "endDate" => date_create_from_format("Y-m-d", $endDate)
                      ];
            $this->ps->addPrice($params);            
        }
    }

    private function addDeviceMetricsData($deviceMetricsData) {
        for($i = 1; $i<count($deviceMetricsData); $i++) {
            $deviceId     = $deviceMetricsData[$i][1];
            $priceId      = $deviceMetricsData[$i][2];
            $totalYield   = $deviceMetricsData[$i][3];
            $monthlyYield = $deviceMetricsData[$i][4];
            $date         = $deviceMetricsData[$i][5];
            
            $params = [
                       "deviceId" => (int)$deviceId,
                       "priceId" => (int)$priceId,
                       "totalYield" => (float)$totalYield,
                       "monthlyYield" => (float)$monthlyYield,
                       "date" => date_create_from_format("Y-m-d", $date)
                      ];
            $this->dms->addDeviceMetrics($params);            
        }
    }

    private function addMonthlyUsedData($monthlyUsedData) {
        for($i = 1; $i<count($monthlyUsedData); $i++) {
            $overallDeviceId = $monthlyUsedData[$i][1];
            $date            = $monthlyUsedData[$i][2];
            $monthlyKwHUsed  = $monthlyUsedData[$i][3];
            
            $params = [
                       "overallDeviceId" => (int)$overallDeviceId,
                       "monthlyKwHUsed" => (float)$monthlyKwHUsed,
                       "date" => date_create_from_format("Y-m-d", $date)
                      ];
            $this->mus->addMonthlyUsed($params);            
        }
    }
}
