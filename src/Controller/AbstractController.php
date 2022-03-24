<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as SymfonyAbstractController;

/**
 * AbstractController
 */
abstract class AbstractController extends SymfonyAbstractController
{
    /**
     * Get json array from Request
     *
     * @param Request $request
     *
     * @return array
     */
    protected function getJsonContentByRequest(Request $request): array
    {
        $json = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON request', JsonResponse::HTTP_BAD_REQUEST);
        }

        return $json;
    }
}
