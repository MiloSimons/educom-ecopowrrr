<?php

namespace App\Repository;

use App\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Price>
 */
class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    public function savePrice($params) {
        
        $price = new Price();
        $price->setBuyInPrice($params["buyInPrice"]);       
        $price->setStartDate($params["startDate"]);
        $price->setEndDate($params["endDate"]);

        $this->getEntityManager()->persist($price);
        $this->getEntityManager()->flush();

        return($price);
    }

    public function fetchPrice($id) {
        return($this->find($id));
    }
}
