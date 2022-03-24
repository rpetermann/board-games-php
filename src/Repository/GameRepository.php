<?php

namespace App\Repository;

use App\Entity\Game;
use App\Exception\GameException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * GameRepository
 */
class GameRepository extends AbstractRepository
{
    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Game::class);
    }

    /**
     * getOneById
     *
     * @param string $id
     * @return Game
     */
    public function getOneById(string $id): Game
    {
        $game = $this->findOneById($id);

        if (empty($game)) {
            throw new GameException(GameException::TYPE_GAME_NOT_FOUND);
        }

        return $game;
    }
}
