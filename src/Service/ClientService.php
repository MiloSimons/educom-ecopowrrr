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

    /*private function fetchAddressInfo($id = null) {
        if(is_null($id)) return(null);
        return($this->artiestRepository->fetchArtiest($id));
    }*/

    private function fetchClient($id = null) {
        if(is_null($id)) return(null);
        return($this->clientRepository->fetchClient($id));
    }

    private function saveClient($params) {
        
        $clientAdvisor = null;
        
        if($params["type"] != "A")
        {
            //$clientAdvisor = $this->fetchClient($params["clientAdvisorId"]);
        }
        $data = [
          "bankAccountNumber" => $params["bankAccountNumber"],
          "zipCode" => $params["zipCode"],
          "houseNumber" => $params["houseNumber"],
          "street" => $params["street"], //use API info for address and geo-info!
          "city" => $params["city"],
          "municipality" => $params["municipality"],
          "province" => $params["province"],
          "longitude" => $params["longitude"],
          "latitude" => $params["latitude"],
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
        $this->ods->saveOverallDevice($client);
    }
}