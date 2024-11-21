<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OverallDevice;
use App\Entity\Device;

use App\Repository\OverallDeviceRepository;
use App\Repository\DeviceRepository;

class DeviceService {

    /** @var DeviceRepository $deviceRepository */    
    private $deviceRepository;
    /** @var OverallDeviceRepository $overallDeviceRepository */    
    private $overallDeviceRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->deviceRepository = $em->getRepository(Device::class);
        $this->overallDeviceRepository = $em->getRepository(OverallDevice::class);
    }

    private function fetchOverallDevice($id = null) {
        if(is_null($id)) return(null);
        return($this->overallDeviceRepository->fetchOverallDevice($id));
    }

    private function saveDevice($params) {
        $data = [
          "serialNumber" => $params["serialNumber"],
          "type" => $params["type"],
          "overallDevice" => $this->fetchOverallDevice($params["overallDeviceId"])        
        ];
        $result = $this->deviceRepository->saveDevice($data);
        return($result);
    }

    public function addDevice($params) {
        $device = $this->saveDevice($params);    
    }
}    