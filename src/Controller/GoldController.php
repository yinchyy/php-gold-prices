<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Config\Definition\Exception\Exception;
use Psr\Log\LoggerInterface;

date_default_timezone_set("Europe/Warsaw");

class GoldController extends AbstractController
{
    #[Route("/api/gold", name: "app_gold", methods: ["POST"])]
    public function index(
        Request $request,
        LoggerInterface $logger
    ): JsonResponse {
        if (!GoldController::isJSONValid($request->getContent())) {
            $logger->error(
                "Request failed: Received message that was not valid JSON."
            );
            return $this->json([
                "message" => "Invalid format, data should be in JSON.",
            ])->setStatusCode("400");
        }

        $timestamps = json_decode($request->getContent(), true);

        if (
            !(
                array_key_exists("from", $timestamps) &&
                array_key_exists("to", $timestamps)
            )
        ) {
            $logger->error(
                "Request failed: Received JSON without 'from' or/and 'to' properties.",
                [$timestamps]
            );
            return $this->json([
                "message" => "Invalid data. Missing 'from' or 'to' property.",
            ])->setStatusCode("400");
        }
        if (
            !(
                GoldController::isValidISO8601Date($timestamps["from"]) &&
                GoldController::isValidISO8601Date($timestamps["to"])
            )
        ) {
            $logger->error(
                "Request failed: Received date in format not matching ISO8601 standard.",
                [$timestamps]
            );
            return $this->json([
                "message" => "Invalid date format. Use ISO8601 date format.",
            ])->setStatusCode("422");
        }
        $startDate = date("Y-m-d", strtotime($timestamps["from"]));
        $endDate = date("Y-m-d", strtotime($timestamps["to"]));
        $url = "http://api.nbp.pl/api/cenyzlota/{$startDate}/{$endDate}";
        $goldPrices = json_decode(@file_get_contents($url), true);
        $sumOfGoldPrices = 0;

        if (is_null($goldPrices)) {
            $logger->error(
                "Request failed: No data was found in API response.",
                [$timestamps]
            );
            return $this->json([
                "message" => "Invalid date range.",
            ])->setStatusCode("400");
        }
        foreach ($goldPrices as $value) {
            $sumOfGoldPrices += $value["cena"];
        }
        $logger->info(
            "Request success: Successful request was made by the user."
        );
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
                "/^(-?(?:[1-9][0-9]*)?[0-9]{4})-(1[0-2]|0[1-9])-(3[01]|0[1-9]|[12][0-9])T(2[0-3]|[01][0-9]):([0-5][0-9]):([0-5][0-9])(\.[0-9]+)?(Z|[+-](?:2[0-3]|[01][0-9]):[0-5][0-9])?$/",
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
            if (is_null(json_decode($json, null))) {
                throw new Exception("Invalid JSON", 1);
            }
            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
