<?php

namespace App\Services\GameRule\RuleHandler\Checkers;

use App\Entity\Game;
use App\Entity\Player;
use App\Helpers\MathTrait;
use App\Services\GameRule\RuleHandler\AbstractGameHandler;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;

/**
 * CheckersHandler
 */
class CheckersHandler extends AbstractGameHandler
{
    use MathTrait;

    const TYPE = 'checkers';
    const EXPECTED_MINIMUN_NUMBER_OF_PLAYERS = 2;
    const EXPECTED_MAXIMUM_NUMBER_OF_PLAYERS = 2;
    const BOARD_ROWS = 8;
    const BOARD_COLUMNS = 8;
    const PIECES_ROWS = 3;
    const FIRST_PLAYER_ROW_START_POSITION = 0;
    const SECOND_PLAYER_ROW_START_POSITION = 5;

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
    public function getBoardRows(): int
    {
        return self::BOARD_ROWS;
    }

    /**
     * @inheritDoc
     */
    public function getBoardColumns(): int
    {
        return self::BOARD_COLUMNS;
    }

    /**
     * @inheritDoc
     */
    public function getPiecesRows(): int
    {
        return self::PIECES_ROWS;
    }

    /**
     * @inheritDoc
     */
    public function isNumberOfPlayersValid(Game $game): bool
    {
        return $this->isReachedMinimumNumberOfPlayers($game) && $this->isReachedMaximumNumberOfPlayers($game);
    }

    /**
     * @inheritDoc
     */
    public function isReachedMaximumNumberOfPlayers(Game $game): bool
    {
        return $game->countPlayers() >= self::EXPECTED_MAXIMUM_NUMBER_OF_PLAYERS;
    }

    /**
     * @inheritDoc
     */
    public function isReachedMinimumNumberOfPlayers(Game $game): bool
    {
        return $game->countPlayers() >= self::EXPECTED_MINIMUN_NUMBER_OF_PLAYERS;
    }

    /**
     * @inheritDoc
     */
    public function createPieces(Game $game): void
    {
        foreach ($game->getPlayer() as $player) {
            $pieces = $this->createPlayerPiece($player);

            $player->setPieces(...$pieces);
        }
    }

    /**
     * createPlayerPiece
     *
     * @param Player $player
     * @return array
     */
    public function createPlayerPiece(Player $player): array
    {
        $pieces = [];

        for ($row = 0; $row < $this->getPiecesRows(); ++$row) {
            for ($column = 0; $column < $this->getBoardColumns(); ++$column) {
                $rowPosition = $player->isFirstPlayer() ? self::FIRST_PLAYER_ROW_START_POSITION : self::SECOND_PLAYER_ROW_START_POSITION;
                $rowPosition += $row;

                $isPieceOnEvenPosition = $this->isSumOfValuesEven($rowPosition, $column);
                if (!$isPieceOnEvenPosition) {
                    continue;
                }

                $piece = $this->pieceFactory->make([
                    'x' => $rowPosition,
                    'y' => $column,
                    'type' => CheckersDefaultPieceHandler::TYPE,
                ]);

                $pieces[] = $piece;
            }
        }

        return $pieces;
    }
}
