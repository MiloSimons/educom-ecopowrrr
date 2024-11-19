<?php

namespace App\Repository;

use App\Entity\Device;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Device>
 */
class DeviceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Device::class);
    }

    public function saveDevice($params) {
        
        $device = new device();
        $device->setOverallDevice($params["overallDevice"]);
        $device->setSerialNumber($params["serialNumber"]);
        $device->setType($params["type"]);

        $this->getEntityManager()->persist($device);
        $this->getEntityManager()->flush();

        return($device);
    }
}
