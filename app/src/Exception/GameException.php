<?php

namespace App\Exception;

use App\Exception\AbstractException;

/**
 * GameException
 */
class GameException extends AbstractException
{
    const TYPE_GAME_NOT_FOUND = 'GAME_NOT_FOUND';
    const TYPE_GAME_REACHED_MAXIMUM_NUMBER_OF_PLAYERS = 'GAME_REACHED_MAXIMUM_NUMBER_OF_PLAYERS';
    const TYPE_INVALID_PAYLOAD = 'INVALID_PAYLOAD';

    /**
     * @inheritDoc
     */
    protected function populateCodeMap(): void
    {
        $this->codeMap = [
            self::TYPE_GAME_NOT_FOUND => 404,
            self::TYPE_GAME_REACHED_MAXIMUM_NUMBER_OF_PLAYERS => 422,
            self::TYPE_INVALID_PAYLOAD => 422,
        ];
    }
}
