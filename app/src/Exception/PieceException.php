<?php

namespace App\Exception;

use App\Exception\AbstractException;

/**
 * PieceException
 */
class PieceException extends AbstractException
{
    const TYPE_PIECE_NOT_FOUND = 'PIECE_NOT_FOUND';
    const TYPE_SLOT_OCCUPIED = 'SLOT_OCCUPIED';
    const TYPE_POSITION_OUT_OF_RANGE = 'POSITION_OUT_OF_RANGE';
    const TYPE_ONLY_DIAGONAL_MOVE_ALLOWED = 'ONLY_DIAGONAL_MOVE_ALLOWED';
    const TYPE_INVALID_DIRECTION = 'INVALID_DIRECTION';
    const TYPE_INVALID_NUMBER_OF_STEPS = 'INVALID_NUMBER_OF_STEPS';
    const TYPE_HAS_PIECE_PROTECTING_JUMP = 'HAS_PIECE_PROTECTING_JUMP';
    const TYPE_DESTINATION_USING_CURRENT_POSITION = 'DESTINATION_USING_CURRENT_POSITION';

    /**
     * @inheritDoc
     */
    protected function populateCodeMap(): void
    {
        $this->codeMap = [
            self::TYPE_PIECE_NOT_FOUND => 404,
            self::TYPE_SLOT_OCCUPIED => 400,
            self::TYPE_POSITION_OUT_OF_RANGE => 400,
            self::TYPE_ONLY_DIAGONAL_MOVE_ALLOWED => 400,
            self::TYPE_INVALID_DIRECTION => 400,
            self::TYPE_INVALID_NUMBER_OF_STEPS => 400,
            self::TYPE_HAS_PIECE_PROTECTING_JUMP => 400,
            self::TYPE_DESTINATION_USING_CURRENT_POSITION => 400,
        ];
    }
}
