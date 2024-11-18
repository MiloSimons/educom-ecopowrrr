<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    public function fetchClient($id) {
        return($this->find($id));
    }

    public function findExistingZipCode($zipCode) {
        $similarZipCodeClient = $this->findOneBy(["zip_code"=>$zipcode]);
        return($similarZipCodeClient);
    }

    public function saveClient($params) {
        
        $client = new Client();
        $client->setBankAccountInfo($params["bankAccountNumber"]);
        $client->setZipCode($params["zipCode"]);
        $client->setHouseNumber($params["houseNumber"]);
        $client->setStreet($params["street"]); //get from external website, first check whether zipcode already exists and and that info!
        $client->setCity($params["city"]);
        $client->setMunicipality($params["municipality"]);
        $client->setProvince($params["province"]);
        $client->setLongitude($params["longitude"]);
        $client->setLatitude($params["latitude"]);
        $client->setFirstName($params["firstName"]);
        $client->setLastName($params["lastName"]);
        $client->setAge($params["age"]);
        $client->setGender($params["gender"]);
        $client->setType($params["type"]);
        $client->setClientAdvisor($params["clientAdvisor"]);

        

        $this->getEntityManager()->persist($client);
        $this->getEntityManager()->flush();

        return($client);
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
