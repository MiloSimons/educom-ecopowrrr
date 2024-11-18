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
        $overallDevice->setTotalKwHUsed(0);
        $overallDevice->setMonthlyKwHUsed(0);

        $this->getEntityManager()->persist($overallDevice);
        $this->getEntityManager()->flush();

        return($overallDevice);
    }

    //    /**
    //     * @return OverallDevice[] Returns an array of OverallDevice objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('o.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?OverallDevice
    //    {
    //        return $this->createQueryBuilder('o')
    //            ->andWhere('o.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
