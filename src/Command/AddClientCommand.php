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
    name: 'addClient',
    description: 'Add a client or client-advisor',
)]
class AddClientCommand extends Command
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
        $bankAccountNumber = $io->ask('Please enter your bank account number:');
        $zipCode = $io->ask('Please enter your zip code:');
        $houseNumber = $io->ask('Please enter your housenumber:');
        $firstName = $io->ask('Please enter your first name:');
        $lastName = $io->ask('Please enter your last name');
        $age = $io->ask('Please enter your age:');
        $gender = $io->ask('Please enter your gender:');
        $type = $io->ask('Enter "A" if you are an advisor, "C" if you are a client:');
        $clientAdvisorId = "";
        if($type == "C"){
            $clientAdvisorId = $io->ask('Please enter the ID of your client advisor:');
        }

        $params = [ "bankAccountNumber"=>$bankAccountNumber,
                    "zipCode"=>$zipCode,
                    "houseNumber"=>$houseNumber,
                    "firstName"=>$firstName,
                    "lastName"=>$lastName,
                    "age"=>$age,
                    "gender"=>$gender,
                    "type"=>$type,
                    "clientAdvisorId"=>$clientAdvisorId
                   ];
        $io->success($params);

        $this->cs->addClient($params);
        
        if($type == "C"){
            $io->success('You have a succesfully created a new client!');
        }
        if($type == "A"){
            $io->success('You have a succesfully created a new client advisor!');
        }

        return Command::SUCCESS;
    }
}
