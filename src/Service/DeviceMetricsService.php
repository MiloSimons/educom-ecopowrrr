<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Device;
use App\Entity\Price;
use App\Entity\Status;
use App\Entity\DeviceMetrics;

use App\Repository\DeviceRepository;
use App\Repository\PriceRepository;
use App\Repository\StatusRepository;
use App\Repository\DeviceMetricsRepository;

class DeviceMetricsService {

    /** @var DeviceMetricsRepository $deviceRepository */    
    private $deviceMetricsRepository;
    /** @var DeviceRepository $deviceRepository */    
    private $deviceRepository;
    /** @var PriceRepository $priceRepository */    
    private $priceRepository;
    /** @var StatusRepository $statusRepository */    
    private $statusRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->deviceMetricsRepository = $em->getRepository(DeviceMetrics::class);
        $this->deviceRepository = $em->getRepository(Device::class);
        $this->priceRepository = $em->getRepository(Price::class);
        $this->statusRepository = $em->getRepository(Status::class);
    }

    private function fetchDevice($id = null) {
        if(is_null($id)) return(null);
        return($this->deviceRepository->fetchDevice($id));
    }

    private function fetchPrice($id = null) {
        if(is_null($id)) return(null);
        return($this->priceRepository->fetchPrice($id));
    }

    private function fetchStatus($status) {
        return($this->statusRepository->fetchStatus($status));
    }

    private function saveDeviceMetrics($params) {

        $data = [
            "totalYield" => $params["totalYield"],
            "monthlyYield" => $params["monthlyYield"],
            "date" => $params["date"],

            "device" => $this->fetchDevice($params["deviceId"]),
            "price" => $this->fetchPrice($params["priceId"]),
            "status" => $this->fetchStatus("active")     
        ];
        $result = $this->deviceMetricsRepository->saveDeviceMetrics($data);
        return($result);
    }

    public function addDeviceMetrics($params) {
        $deviceMetrics = $this->saveDeviceMetrics($params);    
    }
}   