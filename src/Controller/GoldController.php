<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

date_default_timezone_set("Europe/Warsaw");

class GoldController extends AbstractController
{
    #[Route("/api/gold", name: "app_gold", methods: ["POST"])]
    public function index(Request $request): JsonResponse {
        $timestamps = json_decode($request->getContent(), true);
        $startDate = date("Y-m-d", strtotime($timestamps["from"]));
        $endDate = date("Y-m-d", strtotime($timestamps["to"]));
        $url = "http://api.nbp.pl/api/cenyzlota/{$startDate}/{$endDate}";
        $goldPrices = json_decode(file_get_contents($url), true);
        $sumOfGoldPrices=0;
        foreach($goldPrices as $value){
            $sumOfGoldPrices+=$value["cena"];
        }
        return $this->json([
            "from" => date_format(
                date_create($goldPrices[0]["data"]),
                DATE_W3C
            ),
            "to" => date_format(
                date_create($goldPrices[count($goldPrices) - 1]["data"]),
                DATE_W3C
            ),
            "avg" => round($sumOfGoldPrices/count($goldPrices),2),
        ]);
    }
}
