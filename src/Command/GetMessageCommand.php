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

#[AsCommand(
    name: 'getMessage',
    description: 'get message of a client described by his/her zipcode + housenumber of a specific month',
)]
class GetMessageCommand extends Command
{

    public function __construct()
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $zipCode = $io->ask('Please enter your zip code:');
        $houseNumber = $io->ask('Please enter your housenumber:');
        $month = $io->ask('Please enter the month you want your message from:');
        $year = $io->ask('Please enter the year you want your message from:');
        $message = $this->fetchMessage($zipCode, $houseNumber, $month, $year);
        $io->success($message);
        return Command::SUCCESS;
    }

    private function fetchMessage($zipCode, $houseNumber, $month, $year) {
        $curl = curl_init();
        $URL = "http://127.0.0.1:8000/overallDevice/getMessage/zipCode={$zipCode}&houseNumber={$houseNumber}&month={$month}&year={$year}";

        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($curl);
        curl_close($curl);
        return ($result);
    }
}
