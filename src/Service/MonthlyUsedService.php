<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OverallDevice;
use App\Entity\Device;
use App\Entity\MonthlyUsed;

use App\Repository\OverallDeviceRepository;
use App\Repository\MonthlyUsedRepository;

class MonthlyUsedService {

    /** @var OverallDeviceRepository $overallDeviceRepository */    
    private $overallDeviceRepository;

    /** @var MonthlyUsedRepository $monthlyUsedRepository */    
    private $monthlyUsedRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->overallDeviceRepository = $em->getRepository(OverallDevice::class);
        $this->monthlyUsedRepository = $em->getRepository(MonthlyUsed::class);
    }

    private function fetchOverallDevice($id = null) {
        if(is_null($id)) return(null);
        return($this->overallDeviceRepository->fetchOverallDevice($id));
    }

    private function saveMonthlyUsed($params) {
        $data = [
          "date" => $params["date"],
          "monthlyKwHUsed" => $params["monthlyKwHUsed"],
          "overallDevice" => $this->fetchOverallDevice($params["overallDeviceId"])        
        ];
        $result = $this->monthlyUsedRepository->saveMonthlyUsed($data);
        return($result);
    }

    public function addMonthlyUsed($params) {
        $device = $this->saveMonthlyUsed($params);    
    }
}    