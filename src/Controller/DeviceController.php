<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\DeviceService;

#[Route('/device')]
class DeviceController extends AbstractController
{
    private $ds;

    public function __construct(DeviceService $ds) {
        $this->ds = $ds;      
    }

    #[Route('/addDevice', name: 'addDevice')]
    public function addDevice(Request $request) {
        $params = json_decode($request->getContent(),true);        
        $this->ds->addDevice($params);
        $response = new Response(json_encode($params));        
        return ($response);        
    }
}

