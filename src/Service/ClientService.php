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

    public function fetchAllClients() {
        return($this->clientRepository->fetchAllClients());
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
        $data = [
                    "messageId" => $this->randomHash(),
                    "overallDeviceId" => $overallDevice->getId(),
                    "deviceStatus" => $overallDevice->getStatus()->getStatus(),
                    "date" => $month . ' ' . $year,
                    "monthlyUsage" => $this->getMonthlyKwHUsed($overallDevice, $month, $year),
                    "devices" => $this->getAllDeviceInfo($overallDevice, $month, $year)
                ];       
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
                                                    "deviceMonthlyYield" => $deviceMetrics["deviceMonthlyYield"]
                                                ];
        }
        return($devicesInfo);
    }

    private function getDeviceMetricsInfo($device, $month, $year) {
        $devicesMetrics = $device->getDeviceMetrics();
        foreach($devicesMetrics as $deviceMetrics) {
            $date = $deviceMetrics->getDate();
            if($date->format('Y') == $year && $date->format('m') == $month) {
                $allDevicesMetrics = [
                                        "deviceStatus" => $deviceStatus = $deviceMetrics->getStatus()->getStatus(),
                                        "deviceTotalYield" => $deviceMetrics->getTotalYield(),
                                        "deviceMonthlyYield" => $deviceMetrics->getMonthlyYield()
                                    ];
            }
        }
        return($allDevicesMetrics);
    }

    private function getMonthlyKwHUsed($overallDevice, $month, $year) {
        $monthlyUseds = $overallDevice->getMonthlyUseds();
        foreach($monthlyUseds as $monthlyUsed) {
            $date = $monthlyUsed->getDate();
            if($date->format('Y') == $year && $date->format('m') == $month) {
                $monthlyKwHUsed = $monthlyUsed->getMonthlyKwHUsed();
            }
        }
        return($monthlyKwHUsed);
    }

    private function randomHash($len=20) {
        return substr(hash('sha256', openssl_random_pseudo_bytes(20)), -$len);
    }

    public function getSpreadsheet1Info() {
        $allClients = $this->fetchAllClients();
        $spreadsheet1Info = [];
        foreach($allClients as $client) {
            if($client->getType() == "C") {
                $overallDevice =  $this->ods->fetchOverallDeviceByClient($client);
                $devices = $overallDevice->getDevices();
                $monthlyKwHUseds = $overallDevice->getMonthlyUseds();
                $totalOverturn = 0;
                $surplusses = [];
                $monthlyYieldTotal = [];
                $priceMonth = [];

                foreach($devices as $device) {
                    $deviceMetrics = $device->getDeviceMetrics();
                    foreach($deviceMetrics as $metrics) {
                        $monthlyYield = $metrics->getMonthlyYield();                      
                        $month = $metrics->getPrice()->getStartDate()->format('m');
                        $year = $metrics->getPrice()->getStartDate()->format('Y');
                        if(!empty($monthlyYieldTotal[$month.$year])) {
                            $monthlyYieldTotal[$month.$year] += $monthlyYield;
                        } else {
                            $monthlyYieldTotal[$month.$year] = $monthlyYield;
                        }
                        $price = $metrics->getPrice()->getBuyInPrice();
                        $priceMonth[$month.$year] = $price;                  
                    }
                }
                
                foreach($monthlyKwHUseds as $monthlyUsed) {
                    $month = $monthlyUsed->getDate()->format('m');
                    $year = $monthlyUsed->getDate()->format('Y');
                    $monthlyKwHUsed = $monthlyUsed->getMonthlyKwHUsed();
                    if (!empty($monthlyYieldTotal[$month.$year])) {
                        $monthlySurplus = $monthlyYieldTotal[$month.$year] - $monthlyKwHUsed;
                        $surplusses["surplus ".$month." - ".$year] = $monthlySurplus;
                        $monthlyOverturn = $monthlySurplus * $priceMonth[$month.$year];
                        $totalOverturn += $monthlyOverturn;
                    }
                }

                $spreadsheet1Info["client".$client->getId()] = [
                    "firstName" => $client->getFirstName(),
                    "lastName" => $client->getLastName(),
                    "age" => $client->getAge(),
                    "gender" => $client->getGender(),
                    "totalTurnover" => $totalOverturn,
                    "surplusPerMonth" => $surplusses
                ];
            }            
        }
        return($spreadsheet1Info);
    }

    /*private function getMonthlyYieldAndPrice($devices) {
        foreach($devices as $device) {
            $deviceMetrics = $device->getDeviceMetrics();
            foreach($deviceMetrics as $metrics) {
                $monthlyYield = $metrics->getMonthlyYield();                      
                $month = $metrics->getPrice()->getStartDate()->format('m');
                $year = $metrics->getPrice()->getStartDate()->format('Y');
                if(!empty($monthlyYieldTotal[$month.$year])) {
                    $monthlyYieldTotal[$month.$year] += $monthlyYield;
                } else {
                    $monthlyYieldTotal[$month.$year] = $monthlyYield;
                }
                $price = $metrics->getPrice()->getBuyInPrice();
                $priceMonth[$month.$year] = $price;                  
            }
        }
        return($monthlyYieldTotal, $priceMonth);
    }*/

    /*private function calcSurplusAndOverturn($monthlyKwHUseds) {
        foreach($monthlyKwHUseds as $monthlyUsed) {
            $month = $monthlyUsed->getDate()->format('m');
            $year = $monthlyUsed->getDate()->format('Y');
            $monthlyKwHUsed = $monthlyUsed->getMonthlyKwHUsed();
            if (!empty($monthlyYieldTotal[$month.$year])) {
                $monthlySurplus = $monthlyYieldTotal[$month.$year] - $monthlyKwHUsed;
                $surplusses["surplus ".$month." - ".$year] = $monthlySurplus;
                $monthlyOverturn = $monthlySurplus * $priceMonth[$month.$year];
                $totalOverturn += $monthlyOverturn;
            }
        }
        return($totalOverturn, $surplusses);
    }*/
}