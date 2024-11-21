<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Price;

use App\Repository\PriceRepository;


class PriceService {

    /** @var priceRepository $priceRepository */    
    private $priceRepository;

    public function __construct(EntityManagerInterface $em) {
        $this->priceRepository = $em->getRepository(Price::class);
    }

    private function savePrice($params) {
        $data = [   
                    "buyInPrice" => $params["buyInPrice"],
                    "startDate" => $params["startDate"],
                    "endDate" => $params["endDate"]
                ];
        $result = $this->priceRepository->savePrice($data);
        return($result);
    }
    
    public function addPrice($params) {
        $price = $this->savePrice($params);    
    }
}