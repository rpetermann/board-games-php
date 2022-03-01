<?php

namespace App\Model;

use App\Entity\Game;
use App\Entity\Piece;
use App\Entity\Player;
use App\Exception\PlayerException;
use App\Factory\HistoryFactory;
use App\Factory\PlayerFactory;
use App\Services\GameRule\GameRule;
use App\Services\Workflow\WorkflowService;
use Doctrine\Persistence\ManagerRegistry;

/**
 * GameMovePieceModel
 */
class GameMovePieceModel extends AbstractModel
{
    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * __construct
     *
     * @param ManagerRegistry  $registry
     * @param WorkflowService  $workflow
     * @param GameRule         $gameRule
     * @param PlayerFactory    $playerFactory
     * @param HistoryFactory   $historyFactory
     */
    public function __construct(ManagerRegistry $registry, WorkflowService $workflow, GameRule $gameRule, PlayerFactory $playerFactory, HistoryFactory $historyFactory)
    {
        parent::__construct($registry, $workflow, $gameRule, $playerFactory);
        $this->historyFactory = $historyFactory;
    }

    /**
     * process
     *
     * @param string $gameId
     * @param string $playerId
     * @param string $pieceId
     * @param array  $payload
     * @return Game
     */
    public function process(string $gameId, string $playerId, string $pieceId, array $payload): Game
    {
        $piece = $this->pieceRepository->getPlayerPieceInGame(
            $pieceId,
            $playerId,
            $gameId,
        );
        $player = $piece->getPlayer();
        $game = $player->getGame();

        if (!$player->isTurn()) {
            throw new PlayerException(PlayerException::TYPE_IS_NOT_PLAYER_TURN);
        }
        
        $this->movePiece($piece, $game, $payload);

        $this->changeGameStatus($game);
        $this->changePlayersStatus(...$game->getPlayer());

        $this->save($game);

        return $game;
    }

    /**
     * movePiece
     *
     * @param Piece $piece
     * @param Game  $game
     * @param array $data
     * @return void
     */
    public function movePiece(Piece $piece, Game $game, array $data): void
    {
        $this->gameRule->movePiece($piece, $data['toX'], $data['toY']);

        $history = $this->createHistory($game);

        $game->addHistory($history);
    }

    /**
     * changeGameStatus
     *
     * @param Game $game
     * @return void
     */
    protected function changeGameStatus(Game $game): void
    {
        if (!$game->isFinished()) {
            return;
        }
        
        $this->workflow->changeStatus(Game::TRANSITION_FINISHED, $game);

    }

    /**
     * changePlayersStatus
     *
     * @param Player ...$players
     * @return void
     */
    protected function changePlayersStatus(Player ...$players): void
    {
        foreach ($players as $player) {
            $this->workflow->changeStatus($this->getPlayerTransition($player), $player);
        }
    }

    /**
     * getPlayerTransition
     *
     * @param Player $player
     * @return string
     */
    protected function getPlayerTransition(Player $player): string
    {
        $game = $player->getGame();

        if ($game->isFinished()) {
            return $player->hasPieces() ? Player::TRANSITION_WON : Player::TRANSITION_LOST;
        }

        $playerFinishedTurn = $player->isTurn();

        return $playerFinishedTurn ? Player::TRANSITION_WAITING_OPPONENT_PLAY : Player::TRANSITION_WAITING_PLAY;
    }
}
