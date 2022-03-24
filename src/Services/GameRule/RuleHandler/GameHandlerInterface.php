<?php

namespace App\Services\GameRule\RuleHandler;

use App\Entity\Game;
use App\Entity\Player;
use App\Entity\Piece;
use App\Dto\SlotDto;
use Doctrine\Common\Collections\Collection;

/**
 * GameHandlerInterface
 */
interface GameHandlerInterface
{
    /**
     * canProcess
     *
     * @param string $game
     * @return boolean
     */
    public function canProcess(string $game): bool;

    /**
     * getType
     *
     * @return string
     */
    public function getType(): string;

    /**
     * getAllBoardSlots
     *
     * @return Collection<i, SlotDto>
     */
    public function getAllBoardSlots(): Collection;

    /**
     * isNumberOfPlayersValid
     *
     * @param Game $game
     * @return boolean
     */
    public function isNumberOfPlayersValid(Game $game): bool;

    /**
     * isReachedMaximumNumberOfPlayers
     *
     * @param Game $game
     * @return boolean
     */
    public function isReachedMaximumNumberOfPlayers(Game $game): bool;

    /**
     * isReachedMinimumNumberOfPlayers
     *
     * @param Game $game
     * @return boolean
     */
    public function isReachedMinimumNumberOfPlayers(Game $game): bool;

    /**
     * applyPlayersSequenceRandomically
     *
     * @param Player ...$players
     * @return void
     */
    public function applyPlayersSequenceRandomically(Player ...$players): void;

    /**
     * start
     *
     * @param Game $game
     * @return Game
     */
    public function start(Game $game): Game;

    /**
     * createPieces
     *
     * @param Game $game
     * @return void
     */
    public function createPieces(Game $game): void;

    /**
     * getBoardRows
     *
     * @return integer
     */
    public function getBoardRows(): int;

    /**
     * getBoardColumns
     *
     * @return integer
     */
    public function getBoardColumns(): int;

    /**
     * getPiecesRows
     *
     * @return integer
     */
    public function getPiecesRows(): int;

    /**
     * movePiece
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return Piece
     */
    public function movePiece(Piece $piece, int $toX, int $toY): Piece;
}
