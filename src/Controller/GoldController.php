<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoldController extends AbstractController
{
    #[Route('/api/gold', name: 'app_gold', methods: [ 'POST' ])]
    public function index(): JsonResponse
    {
        return $this->json([
            'from' => '2001-01-04T00:00:00+00:00',
            'to' => '2001-01-04T00:00:00+00:00',
            'avg' => 228.1
        ]);
    }
}
