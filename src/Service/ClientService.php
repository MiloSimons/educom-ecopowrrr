<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Client;

use App\Repository\ClientRepository;

use App\Service\OverallDeviceService;


class ClientService {

    /** @var clientRepository $clientRepository */    
    private $clientRepository;

    /** @var overallDeviceService $ods */    
    private $ods;

    public function __construct(EntityManagerInterface $em, OverallDeviceService $ods) {
        $this->clientRepository = $em->getRepository(Client::class);
        $this->ods = $ods;
    }

    private function fetchAddressInfo($zipCode, $houseNumber) {
        $curl = curl_init();
        $URL = "https://postcode.tech/api/v1/postcode/full?postcode={$zipCode}&number={$houseNumber}";
        $key = $_ENV["POSTCODE_TECH_KEY"];

        curl_setopt($curl, CURLOPT_URL, $URL);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($curl, CURLOPT_HTTPHEADER,
            [
                'Authorization: Bearer ' . $key
            ]    
        );

        $result = json_decode(curl_exec($curl), true);

        curl_close($curl);
        
        $addressInfo = [
                        "street" => $result["street"],
                        "city" => $result["city"],
                        "municipality" => $result["municipality"],
                        "province" => $result["province"],
                        "longitude" => $result["geo"]["lon"],
                        "latitude" => $result["geo"]["lat"]
                    ];

        return ($addressInfo);
    }

    private function fetchClient($id = null) {
        if(is_null($id)) return(null);
        return($this->clientRepository->fetchClient($id));
    }

    private function saveClient($params) {
        
        $clientAdvisor = null;
        $addressInfo = $this->fetchAddressInfo($params["zipCode"], $params["houseNumber"]);
        
        if($params["type"] != "A")
        {
            $clientAdvisor = $this->fetchClient($params["clientAdvisorId"]);
        }
            $data = [
                     "bankAccountNumber" => $params["bankAccountNumber"],
                     "zipCode" => $params["zipCode"],
                     "houseNumber" => $params["houseNumber"],

                     "street" => $addressInfo["street"],
                     "city" => $addressInfo["city"],
                     "municipality" => $addressInfo["municipality"],
                     "province" => $addressInfo["province"],
                     "longitude" => $addressInfo["longitude"],
                     "latitude" => $addressInfo["latitude"],

                     "firstName" => $params["firstName"],
                     "lastName" => $params["lastName"],
                     "age" => $params["age"],
                     "gender" => $params["gender"],
                     "type" => $params["type"],
                     "clientAdvisor" => $clientAdvisor
                    ];
        $result = $this->clientRepository->saveClient($data);
        return($result);
    }
    
    public function addClient($params) {
        $client = $this->saveClient($params);
        //only add overall-device for clients, not for client advisors!
        if($params["type"]=="C"){
            $this->ods->saveOverallDevice($client);
        }      
    }

    private function getClientByZipCode($zipCode, $houseNumber) {
        $client = $this->clientRepository->findByZipCode($zipCode, $houseNumber);
        return($client);
    }

    public function getMessage($zipCode, $houseNumber, $month, $year) {
        $client = $this->getClientByZipCode($zipCode, $houseNumber);
        $overallDevice = $this->ods->fetchOverallDeviceByClient($client);
        $deviceInfo = $this->getAllDeviceInfo($overallDevice, $month, $year);     
       
        //dd($deviceInfo);
        $data = [
                    "messageId" => $this->randomHash(),
                    "overallDeviceId" => $overallDevice->getId(),
                    "deviceStatus" => $overallDevice->getStatus()->getStatus(),
                    "date" => $month . ' ' . $year,
                    "devices" => $deviceInfo
                        //total surplus
                        //month surplus
                ];
        dd($data);        
        return($data);
    }

    private function getAllDeviceInfo($overallDevice, $month, $year) {
        $devices = $overallDevice->getDevices();
        $devicesInfo = [];
        foreach($devices as $device) {
            $deviceId = $device->getId();
            $deviceMetrics = $this->getDeviceMetricsInfo($device, $month, $year);
            $devicesInfo["deviceId".$deviceId] = [
                                                    "serialNumber" => $device->getSerialNumber(),
                                                    "type" => $device->getType(),
                                                    "deviceStatus" => $deviceMetrics["deviceStatus"],
                                                    "deviceTotalYield" => $deviceMetrics["deviceTotalYield"],
                                                    "deviceMonthlyYield" => $deviceMetrics["deviceMonthlyYield"],
                                                    "deviceDate" => $deviceMetrics["deviceDate"]
                                                ];
        }
        return($devicesInfo);
    }

    private function getDeviceMetricsInfo($device, $month, $year) {
        $devicesMetrics = $device->getDeviceMetrics();
        foreach($devicesMetrics as $deviceMetrics) {
            $date = $deviceMetrics->getDate();
            if($date->format('Y') == $year && $date->format('m') == $month){
                $deviceMetricsId = $deviceMetrics->getId();
                $allDevicesMetrics = [
                                        "deviceStatus" => $deviceStatus = $deviceMetrics->getStatus()->getStatus(),
                                        "deviceTotalYield" => $deviceMetrics->getTotalYield(),
                                        "deviceMonthlyYield" => $deviceMetrics->getMonthlyYield(),
                                        "deviceDate" => $date
                                    ];
            }
        }
        return($allDevicesMetrics);
    }

    private function randomHash($len=20) {
        return substr(hash('sha256', openssl_random_pseudo_bytes(20)), -$len);
    }
}