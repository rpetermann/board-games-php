<?php

namespace App\Services\GameRule\RuleHandler;

use App\Entity\Game;
use App\Entity\Player;
use App\Dto\SlotDto;
use App\Entity\Piece;
use App\Factory\PieceFactory;
use App\Helpers\MathTrait;
use App\Services\GameRule\PieceRule;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * AbstractGameHandler
 */
abstract class AbstractGameHandler implements GameHandlerInterface
{
    use MathTrait;

    /**
     * @var PieceRule
     */
    protected $pieceRule;

    /**
     * @var PieceFactory
     */
    protected $pieceFactory;

    /**
     * __construct
     *
     * @param PieceRule    $pieceRule
     * @param PieceFactory $pieceFactory
     */
    public function __construct(PieceRule $pieceRule, PieceFactory $pieceFactory)
    {
        $this->pieceRule = $pieceRule;
        $this->pieceFactory = $pieceFactory;
    }

    /**
     * @inheritDoc
     */
    public function canProcess(string $type): bool
    {
        return $type === $this->getType();
    }

    /**
     * @inheritDoc
     */
    abstract public function getType(): string;

    /**
     * getPieceRuleHandler
     *
     * @param string $pieceType
     * @param string $gameType
     * @return PieceHandlerInterface
     */
    public function getPieceRuleHandler(string $pieceType, string $gameType): PieceHandlerInterface
    {
        return $this->pieceRule->getHandlerByType($pieceType, $gameType);
    }

    /**
     * @inheritDoc
     */
    public function applyPlayersSequenceRandomically(Player ...$players): void
    {
        shuffle($players);

        foreach ($players as $sequence => $player) {
            $player->setSequence($sequence);
        }
    }

    /**
     * @inheritDoc
     */
    public function start(Game $game): Game
    {
        $this->createPieces($game);

        return $game;
    }

    /**
     * @inheritDoc
     */
    abstract public function createPieces(Game $game): void;

    /**
     * @inheritDoc
     */
    abstract public function getBoardRows(): int;

    /**
     * @inheritDoc
     */
    abstract public function getBoardColumns(): int;

    /**
     * @inheritDoc
     */
    public function getAllBoardSlots(): Collection
    {
        $slots = [];
        foreach (range(0, $this->getBoardRows()) as $row) {
            foreach (range(0, $this->getBoardColumns()) as $column) {
                if (!$this->isSumOfValuesEven($row, $column)) {
                    continue;
                }

                $slots[] = new SlotDto($row, $column);
            }
        }

        return new ArrayCollection($slots);
    }

    /**
     * @inheritDoc
     */
    public function movePiece(Piece $piece, int $toX, int $toY): Piece
    {
        $pieceRuleHandler = $this->getPieceRuleHandler($piece->getType(), $this->getType());
        $pieceRuleHandler->validate($piece, $toX, $toY);

        $this->captureOpponentPiece($piece, $toX, $toY);

        $piece->setX($toX);
        $piece->setY($toY);

        return $piece;
    }

    /**
     * captureOpponentPiece
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return boolean
     */
    public function captureOpponentPiece(Piece $piece, int $toX, int $toY): bool
    {
        $game = $piece->getGame();
        $player = $piece->getPlayer();

        $opponentPiece = $game->getOpponentPieceByPosition($player->getId(), $toX, $toY);
        if (empty($opponentPiece)) {
            return false;
        }

        foreach ($game->getOpponents($player->getId()) as $opponent) {
            $opponent->removePiece($opponentPiece);
        }

        return true;
    }
}
