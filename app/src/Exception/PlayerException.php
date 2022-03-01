<?php

namespace App\Exception;

use App\Exception\AbstractException;

/**
 * PlayerException
 */
class PlayerException extends AbstractException
{
    const TYPE_PLAYER_NOT_FOUND = 'PLAYER_NOT_FOUND';
    const TYPE_IS_NOT_PLAYER_TURN = 'IS_NOT_PLAYER_TURN';

    /**
     * @inheritDoc
     */
    protected function populateCodeMap(): void
    {
        $this->codeMap = [
            self::TYPE_PLAYER_NOT_FOUND => 404,
            self::TYPE_IS_NOT_PLAYER_TURN => 400,
        ];
    }
}
