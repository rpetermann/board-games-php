<?php

namespace App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler;

use App\Entity\Piece;
use App\Dto\SlotDto;
use App\Exception\PieceException;
use App\Services\GameRule\RuleHandler\PieceHandlerInterface;
use App\Services\GameRule\RuleHandler\Checkers\CheckersHandler;
use App\Helpers\MathTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Throwable;

/**
 * AbstractCheckersPiece
 */
abstract class AbstractCheckersPiece implements PieceHandlerInterface
{
    use MathTrait;

    const GAME_TYPE = CheckersHandler::TYPE;
    const SLOTS_AROUND_PIECE_RANGE = [
        ['x' => 1, 'y' => 1],
        ['x' => 1, 'y' => -1],
        ['x' => -1, 'y' => -1],
        ['x' => -1, 'y' => 1],
    ];

    /**
     * @inheritDoc
     */
    public function canProcess(string $pieceType, string $gameType): bool
    {
        return $pieceType === $this->getType() && $gameType === $this->getGameType();
    }

    /**
     * @inheritDoc
     */
    public function getGameType(): string
    {
        return self::GAME_TYPE;
    }

    /**
     * @inheritDoc
     */
    abstract public function getType(): string;

    /**
     * Undocumented function
     *
     * @param Piece   $piece
     * @param SlotDto ...$boardSlots
     * @return Collection<int, SlotDto>
     */
    public function getAllowedPieceMoves(Piece $piece, SlotDto ...$boardSlots): Collection
    {
        $allowedMoves = [];

        foreach ($boardSlots as $slot) {
            $slotPositionX = $slot->getX();
            $slotPositionY = $slot->getY();

            try {
                $this->validate($piece, $slotPositionX, $slotPositionY);
            } catch (Throwable $th) {
                continue;
            }

            $allowedMoves[] = new SlotDto($slotPositionX, $slotPositionY);
        }

        return new ArrayCollection($allowedMoves);
    }

    /**
     * @inheritDoc
     */
    public function validate(Piece $piece, int $toX, int $toY): void
    {
        if ($this->isDestinationUsingCurrentPosition($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_DESTINATION_USING_CURRENT_POSITION);
        }

        if ($this->isPositionOutOfBoardRange($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_SLOT_OCCUPIED);
        }

        if ($this->isSlotOccupied($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_SLOT_OCCUPIED);
        }

        if (!$this->isDiagonalMove($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_ONLY_DIAGONAL_MOVE_ALLOWED);
        }

        if (!$this->isDirectionAllowed($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_INVALID_DIRECTION);
        }

        if (!$this->isNumberOfStepsAllowed($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_INVALID_NUMBER_OF_STEPS);
        }

        if ($this->hasPieceProtectingJump($piece, $toX, $toY)) {
            throw new PieceException(PieceException::TYPE_HAS_PIECE_PROTECTING_JUMP);
        }
    }

    /**
     * isDestinationUsingCurrentPosition
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function isDestinationUsingCurrentPosition(Piece $piece, int $toX, int $toY): bool
    {
        return $toX === $piece->getX() && $toY === $piece->getY();
    }

    /**
     * isPositionOutOfBoardRange
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function isPositionOutOfBoardRange(Piece $piece, int $toX, int $toY): bool
    {
        $board = $piece->getBoard();

        $isNegativeNumber = $toX < 0 || $toY < 0;
        if ($isNegativeNumber) {
            return true;
        }

        $isOutOfRangeOnX = $toX >= $board->getRows();
        $isOutOfRangeOnY = $toY >= $board->getColumns();

        return $isOutOfRangeOnX || $isOutOfRangeOnY;
    }

    /**
     * isSlotOccupied
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function isSlotOccupied(Piece $piece, int $toX, int $toY): bool
    {
        $player = $piece->getPlayer();

        return $player->getPieceByPosition($toX, $toY) instanceof Piece;
    }

    /**
     * isDiagonalMove
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function isDiagonalMove(Piece $piece, int $toX, int $toY): bool
    {
        $moveDiffX = abs($piece->getX() - $toX);
        $moveDiffY = abs($piece->getY() - $toY);

        return $moveDiffX === $moveDiffY;
    }

    /**
     * hasPieceProtectingJump
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function hasPieceProtectingJump(Piece $piece, int $toX, int $toY): bool
    {
        $currentPlayer = $piece->getPlayer();
        $game = $currentPlayer->getGame();

        $opponentPiece = $game->getOpponentPieceByPosition($currentPlayer->getId(), $toX, $toY);
        if (empty($opponentPiece)) {
            return false;
        }

        foreach (self::SLOTS_AROUND_PIECE_RANGE as $slotRange) {
            $slotPositionX = $this->calculateSlotPosition($slotRange['x'], $opponentPiece->getX());
            $slotPositionY = $this->calculateSlotPosition($slotRange['y'], $opponentPiece->getY());

            $pieceProtecting = $game->getPieceByPosition(
                $slotPositionX,
                $slotPositionY
            );
            
            if ($pieceProtecting instanceof Piece && $piece !== $pieceProtecting) {
                return true;
            }
        }

        return false;
    }

    /**
     * isDirectionAllowed
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    abstract public function isDirectionAllowed(Piece $piece, int $toX, int $toY): bool;

    /**
     * isNumberOfStepsAllowed
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    abstract public function isNumberOfStepsAllowed(Piece $piece, int $toX, int $toY): bool;

    /**
     * calculateSlotPosition
     *
     * @param integer $slotRange
     * @param integer $piecePosition
     * @return integer
     */
    protected function calculateSlotPosition(int $slotRange, int $piecePosition): int
    {
        return $slotRange > 0 ? $piecePosition + $slotRange : $piecePosition - abs($slotRange);
    }
}
