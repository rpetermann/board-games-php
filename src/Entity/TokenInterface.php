<?php

namespace App\Entity;

/**
 * TokenInterface
 */
interface TokenInterface
{
    /**
     * getAccessToken
     *
     * @return string
     */
    public function getAccessToken(): string;
}
