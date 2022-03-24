<?php

namespace App\Model;

use App\Dto\HistoryDto;
use App\Entity\AbstractEntity;
use App\Entity\Game;
use App\Entity\History;
use App\Entity\Piece;
use App\Entity\Player;
use App\Factory\PlayerFactory;
use App\Repository\GameRepository;
use App\Repository\PlayerRepository;
use App\Repository\PieceRepository;
use App\Services\GameRule\GameRule;
use App\Services\Workflow\WorkflowService;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;

/**
 * AbstractModel
 */
abstract class AbstractModel
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var WorkflowService
     */
    protected $workflow;

    /**
     * @var GameRule
     */
    protected $gameRule;

    /**
     * @var PlayerFactory
     */
    protected $playerFactory;

    /**
     * @var GameRepository
     */
    protected $gameRepository;

    /**
     * @var PlayerRepository
     */
    protected $playerRepository;

    /**
     * @var PieceRepository
     */
    protected $pieceRepository;

    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     * @param WorkflowService $workflow
     * @param GameRule        $gameRule
     * @param PlayerFactory $playerFactory
     */
    public function __construct(ManagerRegistry $registry, WorkflowService $workflow, GameRule $gameRule, PlayerFactory $playerFactory)
    {
        $this->em = $registry->getManager();
        $this->workflow = $workflow;
        $this->gameRule = $gameRule;
        $this->playerFactory = $playerFactory;
        $this->gameRepository = $this->em->getRepository(Game::class);
        $this->playerRepository = $this->em->getRepository(Player::class);
        $this->pieceRepository = $this->em->getRepository(Piece::class);
    }

    /**
     * persist
     *
     * @param AbstractEntity $subject
     * @return void
     */
    public function persist(AbstractEntity $subject): void
    {
        $this->em->persist($subject);
    }

    /**
     * flush
     *
     * @return void
     */
    public function flush(): void
    {
        $this->em->flush();
    }

    /**
     * save
     *
     * @param AbstractEntity $subject
     * @return void
     */
    public function save(AbstractEntity $subject): void
    {
        $this->persist($subject);
        $this->flush();
    }

    /**
     * createHistory
     *
     * @param Game $game
     * @return History
     */
    public function createHistory(Game $game): History
    {
        $historyDto = new HistoryDto($game);

        return $this->historyFactory->make($historyDto);
    }

    /**
     * createPlayer
     *
     * @param array     $payload
     * @param Game|null $game
     * @return Player
     */
    protected function createPlayer(array $payload = [], ?Game $game = null): Player
    {
        $countPlayersInGame = empty($game) ? 0 : $game->countPlayers();

        return $this->playerFactory->make($payload, $countPlayersInGame);
    }
}
