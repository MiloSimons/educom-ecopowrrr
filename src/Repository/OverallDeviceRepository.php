<?php

namespace App\Repository;

use App\Entity\OverallDevice;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<OverallDevice>
 */
class OverallDeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, OverallDevice::class);
    }

    public function saveOverallDevice($params) {
        $overallDevice = new OverallDevice();
        $overallDevice->setClient($params["client"]);
        $overallDevice->setStatus($params["status"]);

        $this->getEntityManager()->persist($overallDevice);
        $this->getEntityManager()->flush();

        return($overallDevice);
    }

    public function fetchOverallDevice($id) {
        return($this->find($id));
    }

    public function fetchOverallDeviceByClient($client) {
        return($this->findOneBy(['client' => $client]));
    }
}
