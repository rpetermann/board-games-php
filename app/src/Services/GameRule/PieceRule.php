<?php

namespace App\Services\GameRule;

use App\Services\GameRule\Exception\PieceRuleException;
use App\Services\GameRule\RuleHandler\PieceHandlerInterface;

/**
 * PieceRule
 */
class PieceRule
{
    /**
     * @var PieceHandlerInterface[]
     */
    protected $handlers;

    /**
     * addHandler
     *
     * @param PieceHandlerInterface $handler
     * @return void
     */
    public function addHandler(PieceHandlerInterface $handler): void
    {
        $this->handlers[get_class($handler)] = $handler;
    }

    /**
     * getHandlerByType
     *
     * @param string $pieceType
     * @param string $gameType
     * @return PieceHandlerInterface
     */
    public function getHandlerByType(string $pieceType, string $gameType): PieceHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->canProcess($pieceType, $gameType)) {
                continue;
            }

            return $handler;
        }

        throw new PieceRuleException(PieceRuleException::TYPE_HANDLER_NOT_FOUND);
    }
}
