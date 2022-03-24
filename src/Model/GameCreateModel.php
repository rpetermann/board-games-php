<?php

namespace App\Model;

use App\Entity\Board;
use App\Entity\Game;
use App\Entity\Player;
use App\Factory\BoardFactory;
use App\Factory\GameFactory;
use App\Factory\PlayerFactory;
use App\Services\GameRule\GameRule;
use App\Services\Workflow\WorkflowService;
use Doctrine\Persistence\ManagerRegistry;

/**
 * GameCreateModel
 */
class GameCreateModel extends AbstractModel
{
    /**
     * @var GameFactory
     */
    protected $gameFactory;

    /**
     * @var BoardFactory
     */
    protected $boardFactory;

    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     * @param WorkflowService $workflow
     * @param GameRule        $gameRule
     * @param PlayerFactory   $playerFactory
     * @param GameFactory     $gameFactory
     * @param BoardFactory    $boardFactory
     */
    public function __construct(ManagerRegistry $registry, WorkflowService $workflow, GameRule $gameRule, PlayerFactory $playerFactory, GameFactory $gameFactory, BoardFactory $boardFactory)
    {
        parent::__construct($registry, $workflow, $gameRule, $playerFactory);
        $this->gameFactory = $gameFactory;
        $this->boardFactory = $boardFactory;
    }

    /**
     * process
     *
     * @param array $payload
     * @return Game
     */
    public function process(array $payload): Game
    {
        $board = $this->createBoard($payload);
        $player = $this->createPlayer();
        $game = $this->createGame($payload, $board, $player);

        $this->save($game);

        return $game;
    }

    /**
     * createGame
     *
     * @param array $payload
     * @param Board $board
     * @return Game
     */
    protected function createGame(array $payload, Board $board, Player $player): Game
    {
        return $this->gameFactory->make($payload, null, $board, $player);
    }

    /**
     * createBoard
     *
     * @param array $payload
     * @return Board
     */
    protected function createBoard(array $payload): Board
    {
        return $this->boardFactory->makeByType($payload['type'] ?? '');
    }
}
