<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class GoldController extends AbstractController
{
    #[Route('/api/gold', name: 'app_gold', methods: [ 'POST' ])]
    public function index(Request $request): JsonResponse
    {
        $timestamps = json_decode($request->getContent(),true);
        $startDate = date('Y-m-d',strtotime($timestamps["from"]));
        $endDate = date('Y-m-d',strtotime($timestamps["to"]));
        $url = "http://api.nbp.pl/api/cenyzlota/{$startDate}/{$endDate}";
        return $this->json([
            'from' => '2001-01-04T00:00:00+00:00',
            'to' => '2001-01-04T00:00:00+00:00',
            'avg' => 228.1,
            'response' => json_decode(file_get_contents($url))
        ]);
    }
}
