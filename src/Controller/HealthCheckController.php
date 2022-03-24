<?php

namespace App\Controller;

use App\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * HealthCheckController
 */
class HealthCheckController extends AbstractController
{
    /**
     * health
     *
     * @return JsonResponse
     */
    public function ping(): JsonResponse
    {
        return $this->json("pong", JsonResponse::HTTP_OK);
    }
}
