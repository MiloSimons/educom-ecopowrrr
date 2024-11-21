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

use App\Service\DeviceService;

#[AsCommand(
    name: 'addDevice',
    description: 'Add a new device',
)]
class AddDeviceCommand extends Command
{
    private $ds;

    public function __construct(DeviceService $ds)
    {
        parent::__construct();
        $this->ds = $ds;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $serialNumber = $io->ask('Please enter the serial number of your device:');
        $type = $io->ask('Please enter the type of device you wish to add:');
        $overallDeviceId = $io->ask('Please enter the ID of your overall device:');

        $params = [
                    "serialNumber"=>$serialNumber,
                    "type"=>$type,
                    "overallDeviceId"=>$overallDeviceId
                  ];
        $io->success($params);

        $this->ds->addDevice($params);
        $io->success('You succesfully added a new device!');
        
        return Command::SUCCESS;
    }
}
