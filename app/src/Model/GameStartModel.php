<?php

namespace App\Model;

use App\Entity\Game;
use App\Entity\Player;
use App\Factory\HistoryFactory;
use App\Factory\PlayerFactory;
use App\Model\AbstractModel;
use App\Services\GameRule\GameRule;
use App\Services\Workflow\WorkflowService;
use Doctrine\Persistence\ManagerRegistry;

/**
 * GameStartModel
 */
class GameStartModel extends AbstractModel
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
     * @return Game
     */
    public function process(string $gameId): Game
    {
        $game = $this->gameRepository->getOneById($gameId);
        
        $this->applyPlayersSequenceRandomically($game);

        $this->startGame($game);

        $this->changeGameStatusToPlaying($game);
        $this->changePlayersStatusToPlaying(...$game->getPlayer());

        $this->save($game);

        return $game;
    }

    /**
     * applyPlayersSequenceRandomically
     *
     * @param Game $game
     * @return void
     */
    public function applyPlayersSequenceRandomically(Game $game): void
    {
        $this->gameRule->applyPlayersSequenceRandomically($game);
    }

    /**
     * startGame
     *
     * @param Game $game
     * @return void
     */
    public function startGame(Game $game): void
    {
        $game = $this->gameRule->start($game);

        $history = $this->createHistory($game);

        $game->addHistory($history);
    }

    /**
     * changeGameStatusToPlaying
     *
     * @param Game $game
     * @return void
     */
    protected function changeGameStatusToPlaying(Game $game): void
    {
        $this->workflow->changeStatus(Game::TRANSITION_PLAYING, $game);
    }

    /**
     * changePlayersStatusToPlaying
     *
     * @param Player ...$players
     * @return void
     */
    protected function changePlayersStatusToPlaying(Player ...$players): void
    {
        foreach ($players as $player) {
            $transition = $player->isFirstPlayer() ? Player::TRANSITION_WAITING_PLAY : Player::TRANSITION_WAITING_OPPONENT_PLAY;

            $this->workflow->changeStatus($transition, $player);
        }
    }
}
