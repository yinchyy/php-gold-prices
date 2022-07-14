<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;

date_default_timezone_set("Europe/Warsaw");

class GoldController extends AbstractController
{
    #[Route("/api/gold", name: "app_gold", methods: ["POST"])]
    public function index(Request $request): JsonResponse {
        if(!GoldController::isJSONValid($request->getContent())){
            return $this->json([])->setStatusCode('400');
        }
        $timestamps = json_decode($request->getContent(), true);
        $startDate = date("Y-m-d", strtotime($timestamps["from"]));
        $endDate = date("Y-m-d", strtotime($timestamps["to"]));
        $url = "http://api.nbp.pl/api/cenyzlota/{$startDate}/{$endDate}";
        $goldPrices = json_decode(file_get_contents($url), true);
        $sumOfGoldPrices = 0;
        foreach ($goldPrices as $value) {
            $sumOfGoldPrices += $value["cena"];
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
            "avg" => round($sumOfGoldPrices / count($goldPrices), 2),
        ]);
    }
    public static function isValidISO8601Date(string $date): bool
    {
        if (
            preg_match(
                "/\d{4}-\d\d-\d\dT\d\d:\d\d:\d\d(\.\d+)?(([+-]\d\d:\d\d)|Z)?/i",
                $date
            )
        ) {
            return true;
        }
        return false;
    }
    public static function isJSONValid(string $json): bool
    {
        try {
            if(is_null(json_decode($json, null))){
                throw new Exception("Invalid JSON", 1);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
