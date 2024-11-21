<?php

namespace App\Repository;

use App\Entity\DeviceMetrics;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<DeviceMetrics>
 */
class DeviceMetricsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DeviceMetrics::class);
    }

    public function saveDeviceMetrics($params) {
        
        $deviceMetrics = new DeviceMetrics();
        $deviceMetrics->setDevice($params["device"]);
        $deviceMetrics->setPrice($params["price"]);
        $deviceMetrics->setStatus($params["status"]);

        $deviceMetrics->setTotalYield($params["totalYield"]);
        $deviceMetrics->setMonthlyYield($params["monthlyYield"]);
        $deviceMetrics->setDate($params["date"]);

        $this->getEntityManager()->persist($deviceMetrics);
        $this->getEntityManager()->flush();

        return($deviceMetrics);
    }
}
