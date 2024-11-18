<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\OverallDevice;
use App\Entity\Status;

use App\Repository\OverallDeviceRepository;
use App\Repository\StatusRepository;

//use App\Service\ClientService;

class OverallDeviceService {

    /** @var OverallDeviceRepository $overallDeviceRepository */    
    private $overallDeviceRepository;
    /** @var StatusRepository $statusRepository */    
    private $statusRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->overallDeviceRepository = $em->getRepository(OverallDevice::class);
        $this->statusRepository = $em->getRepository(Status::class);

    }

    /*private function fetchOverallDevice($id = null) {
        if(is_null($id)) return(null);
        return($this->overallDeviceRepository->fetchOverallDevice($id));
    }*/

    private function fetchStatus($status) {
        return($this->statusRepository->fetchStatus($status));
    }

    public function saveOverallDevice($client) {
        $data = [
          "client" => $client,
          "status" => $this->fetchStatus("active"),          
        ];

        $result = $this->overallDeviceRepository->saveOverallDevice($data);
        return($result);
    }
}