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
    private $as;

    public function __construct(ClientService $cs)
    {
        parent::__construct();
        $this->cs = $cs;
    }

    protected function configure(): void
    {
        /*$this
            ->addArgument('bankAccountNumber', InputArgument::OPTIONAL, 'Bank Account Number')
            ->addArgument('zipCode', InputArgument::OPTIONAL, 'Zip code')
            ->addArgument('houseNumber', InputArgument::OPTIONAL, 'House number')
            ->addArgument('street', InputArgument::OPTIONAL, 'Street')
            ->addArgument('city', InputArgument::OPTIONAL, 'City')
            ->addArgument('municipality', InputArgument::OPTIONAL, 'Municipality')
            ->addArgument('province', InputArgument::OPTIONAL, 'Province')
            ->addArgument('longitude', InputArgument::OPTIONAL, 'Longitude')
            ->addArgument('latitude', InputArgument::OPTIONAL, 'Latitude')
            ->addArgument('firstName', InputArgument::OPTIONAL, 'First name')
            ->addArgument('lastName', InputArgument::OPTIONAL, 'Last name')
            ->addArgument('age', InputArgument::OPTIONAL, 'Age')
            ->addArgument('gender', InputArgument::OPTIONAL, 'Gender')
            ->addArgument('type', InputArgument::OPTIONAL, 'Type: C = Client, A = Advisor')
            ->addArgument('clientAdvisor', InputArgument::OPTIONAL, 'Client Advisor ID (Optional)')
        ;*/
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $bankAccountNumber = $io->ask('Please enter your bank account number:');
        $zipCode = $io->ask('Please enter your zip code:');
        $houseNumber = $io->ask('Please enter your housenumber:');
        $street = $io->ask('Please enter your street:');
        $city = $io->ask('Please enter your city:');
        $municipality = $io->ask('Please enter your municipality:');
        $province = $io->ask('Please your province:');
        $longitude = $io->ask('Please your longitude:');
        $latitude = $io->ask('Please enter your latitude');
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
                    "street"=>$street,
                    "city"=>$city,
                    "municipality"=>$municipality,
                    "province"=>$province,
                    "longitude"=>$longitude,
                    "latitude"=>$latitude,
                    "firstName"=>$firstName,
                    "lastName"=>$lastName,
                    "age"=>$age,
                    "gender"=>$gender,
                    "type"=>$type,
                    "clientAdvisorId"=>$clientAdvisorId
                   ];
        $io->success($params);
        $io->success('You have a succesfully created a new client (advisor)!');
        $this->cs->addClient($params);
        return Command::SUCCESS;
    }
}
