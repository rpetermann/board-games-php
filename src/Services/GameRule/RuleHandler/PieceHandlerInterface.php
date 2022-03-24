<?php

namespace App\Services\GameRule\RuleHandler;

use App\Entity\Piece;
use App\Dto\SlotDto;
use Doctrine\Common\Collections\Collection;

/**
 * PieceHandlerInterface
 */
interface PieceHandlerInterface
{
    /**
     * canProcess
     *
     * @param string $pieceType
     * @param string $gameType
     * @return boolean
     */
    public function canProcess(string $pieceType, string $gameType): bool;

    /**
     * getType
     *
     * @return string
     */
    public function getType(): string;

    /**
     * getGameType
     *
     * @return string
     */
    public function getGameType(): string;

    /**
     * getAllowedPieceMoves
     *
     * @param Piece   $piece
     * @param SlotDto ...$boardSlots
     * @return Collection<int, SlotDto>
     */
    public function getAllowedPieceMoves(Piece $piece, SlotDto ...$boardSlots): Collection;

    /**
     * validate
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return void
     */
    public function validate(Piece $piece, int $toX, int $toY): void;
}
