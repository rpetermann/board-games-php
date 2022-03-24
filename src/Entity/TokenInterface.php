<?php

namespace App\Entity;

/**
 * TokenInterface
 */
interface TokenInterface
{
    public function getAccessToken(): string;
}
