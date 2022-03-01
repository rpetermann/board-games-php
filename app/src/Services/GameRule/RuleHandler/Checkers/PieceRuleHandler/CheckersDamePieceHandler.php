<?php

namespace App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler;

use App\Entity\Piece;

/**
 * CheckersDamePieceHandler
 */
class CheckersDamePieceHandler extends AbstractCheckersPiece
{
    const TYPE = 'dame';

    /**
     * @inheritDoc
     */
    public function getType(): string
    {
        return self::TYPE;
    }

    /**
     * @inheritDoc
     */
    public function isDirectionAllowed(Piece $piece, int $toX, int $toY): bool
    {
        return true;
    }

    /**
     * @inheritDoc
     */
    public function isNumberOfStepsAllowed(Piece $piece, int $toX, int $toY): bool
    {
        return true;
    }
}
