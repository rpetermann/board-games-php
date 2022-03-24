<?php

namespace App\Model;

use App\Entity\Game;
use App\Exception\GameException;

/**
 * GameAddPlayerModel
 */
class GameAddPlayerModel extends AbstractModel
{
     /**
     * process
     *
     * @param string $gameId
     * @param array  $payload
     * @return Game
     */
    public function process(string $gameId, array $payload): Game
    {
        $game = $this->gameRepository->getOneById($gameId);

        if ($this->isGameReachedMaximumNumberOfPlayers($game)) {
            throw new GameException(GameException::TYPE_GAME_REACHED_MAXIMUM_NUMBER_OF_PLAYERS);
        }

        $player = $this->createPlayer($payload, $game);
        $game->addPlayer($player);

        $this->changeGameStatusToWaitingStart($game);

        $this->save($game);

        return $game;
    }

    /**
     * isGameReachedMaximumNumberOfPlayers
     *
     * @param Game $game
     * @return boolean
     */
    protected function isGameReachedMaximumNumberOfPlayers(Game $game): bool
    {
        return $this->gameRule->isReachedMaximumNumberOfPlayers($game);
    }

    /**
     * changeGameStatusToWaitingStart
     *
     * @param Game $game
     * @return void
     */
    protected function changeGameStatusToWaitingStart(Game $game): void
    {
        if (!$this->gameRule->isReachedMaximumNumberOfPlayers($game)) {
            return;
        }

        $this->workflow->changeStatus(Game::TRANSITION_WAITING_START, $game);
    }
}
