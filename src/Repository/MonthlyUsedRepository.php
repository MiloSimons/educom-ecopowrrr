<?php

namespace App\Repository;

use App\Entity\MonthlyUsed;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<MonthlyUsed>
 */
class MonthlyUsedRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, MonthlyUsed::class);
    }

    public function saveMonthlyUsed($params) {
        
        $monthlyUsed = new MonthlyUsed();
        $monthlyUsed->setOverallDevice($params["overallDevice"]);
        $monthlyUsed->setDate($params["date"]);
        $monthlyUsed->setMonthlyKwHUsed($params["monthlyKwHUsed"]);

        $this->getEntityManager()->persist($monthlyUsed);
        $this->getEntityManager()->flush();

        return($monthlyUsed);
    }
}
