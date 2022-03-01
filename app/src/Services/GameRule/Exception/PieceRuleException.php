<?php

namespace App\Services\GameRule\Exception;

use App\Exception\AbstractException;

/**
 * PieceRuleException
 */
class PieceRuleException extends AbstractException
{
    const TYPE_HANDLER_NOT_FOUND = 'HANDLER_NOT_FOUND';

    /**
     * @inheritDoc
     */
    protected function populateCodeMap(): void
    {
        $this->codeMap = [
            self::TYPE_HANDLER_NOT_FOUND => 404,
        ];
    }
}
