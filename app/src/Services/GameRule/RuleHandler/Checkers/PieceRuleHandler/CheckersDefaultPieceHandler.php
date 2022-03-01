<?php

namespace App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler;

use App\Entity\Piece;

/**
 * CheckersDefaultPieceHandler
 */
class CheckersDefaultPieceHandler extends AbstractCheckersPiece
{
    const TYPE = 'default';

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
        $player = $piece->getPlayer();
        $isPiecesMustHavePositiveValue = $player->isFirstPlayer();
        $isMovePositive = $this->isSubtractionOfValuesPositive($toX, $piece->getX());

        return $isPiecesMustHavePositiveValue === $isMovePositive;
    }

    /**
     * @inheritDoc
     */
    public function isNumberOfStepsAllowed(Piece $piece, int $toX, int $toY): bool
    {
        $isOnly1StepOnPositionX = 1 === abs($toX - $piece->getX());
        $isOnly1StepOnPositionY = 1 === abs($toY - $piece->getY());

        return $isOnly1StepOnPositionX && $isOnly1StepOnPositionY;
    }
}
