<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;

use App\Service\ClientService;

#[Route('/client')]
class ClientController extends AbstractController
{
    private $cs;

    public function __construct(ClientService $cs) {
        $this->cs = $cs;      
    }

    #[Route('/addClient', name: 'addClient')]
    public function addClient(Request $request) {
        $params = json_decode($request->getContent(),true);        
        $this->cs->addClient($params);
        $response = new Response(json_encode($params));        
        return ($response);        
    }
}
