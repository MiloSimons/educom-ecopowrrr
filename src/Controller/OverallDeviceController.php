<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Service\ClientService;

#[Route('/overallDevice')]
class OverallDeviceController extends AbstractController
{
    private $cs;

    public function __construct(ClientService $cs) {
        $this->cs = $cs;      
    }

    #[Route('/getMessage/zipCode={zipCode}&houseNumber={houseNumber}&month={month}&year={year}', name: 'getMessage')] //add date? or date always being today's date?
    public function getMessage($zipCode, $houseNumber, $month, $year) {
        $overallDevice = $this->cs->getMessage($zipCode, $houseNumber, $month, $year);
        $response = new Response(json_encode($overallDevice));        
        return ($response);
    }

}
