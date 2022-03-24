<?php

namespace App\Services\GameRule;

use App\Dto\SlotDto;
use App\Entity\Game;
use App\Entity\Piece;
use App\Services\GameRule\Exception\GameRuleException;
use App\Services\GameRule\RuleHandler\GameHandlerInterface;
use App\Services\GameRule\RuleHandler\PieceHandlerInterface;
use App\Services\GameRule\PieceRule;
use Doctrine\Common\Collections\Collection;

/**
 * GameRule
 */
class GameRule
{
    /**
     * @var GameHandlerInterface[]
     */
    protected $handlers;

    /**
     * __construct
     *
     * @param PieceRule $pieceRule
     */
    public function __construct(PieceRule $pieceRule)
    {
        $this->pieceRule = $pieceRule;
    }

    /**
     * addHandler
     *
     * @param GameHandlerInterface $handler
     * @return void
     */
    public function addHandler(GameHandlerInterface $handler): void
    {
        $this->handlers[get_class($handler)] = $handler;
    }

    /**
     * getHandlerByType
     *
     * @param string $type
     * @return GameHandlerInterface
     */
    public function getHandlerByType(string $type): GameHandlerInterface
    {
        foreach ($this->handlers as $handler) {
            if (!$handler->canProcess($type)) {
                continue;
            }

            return $handler;
        }

        throw new GameRuleException(GameRuleException::TYPE_HANDLER_NOT_FOUND);
    }

    /**
     * getPieceRuleByType
     *
     * @param string $pieceType
     * @param string $gameType
     * @return PieceHandlerInterface
     */
    public function getPieceRuleByType(string $pieceType, string $gameType): PieceHandlerInterface
    {
        return $this->pieceRule->getHandlerByType($pieceType, $gameType);
    }

    /**
     * isReachedMaximumNumberOfPlayers
     *
     * @param Game $game
     * @return boolean
     */
    public function isReachedMaximumNumberOfPlayers(Game $game): bool
    {
        $handler = $this->getHandlerByType($game->getType());

        return $handler->isReachedMaximumNumberOfPlayers($game);
    }

    /**
     * applyPlayersSequenceRandomically
     *
     * @param Game $game
     * @return void
     */
    public function applyPlayersSequenceRandomically(Game $game): void
    {
        $handler = $this->getHandlerByType($game->getType());

        $handler->applyPlayersSequenceRandomically(...$game->getPlayer());
    }

    /**
     * start
     *
     * @param Game $game
     * @return Game
     */
    public function start(Game $game): Game
    {
        $handler = $this->getHandlerByType($game->getType());

        return $handler->start($game);
    }

    /**
     * movePiece
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return Piece
     */
    public function movePiece(Piece $piece, int $toX, int $toY): Piece
    {
        $game = $piece->getGame();
        $gameRule = $this->getHandlerByType($game->getType());

        return $gameRule->movePiece($piece, $toX, $toY);
    }

    /**
     * getAllowedPieceMoves
     *
     * @param Piece $piece
     * @return Collection<int, SlotDto>
     */
    public function getAllowedPieceMoves(Piece $piece): Collection
    {
        $game = $piece->getGame();
        $gameRule = $this->getHandlerByType($game->getType());
        $pieceRule = $this->getPieceRuleByType($piece->getType(), $gameRule->getType());

        return $pieceRule->getAllowedPieceMoves($piece, ...$gameRule->getAllBoardSlots());
    }
}
