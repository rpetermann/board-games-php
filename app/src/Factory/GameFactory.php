<?php

namespace App\Factory;

use App\Entity\Game;
use App\Entity\Board;
use App\Entity\Player;

/**
 * GameFactory
 */
class GameFactory extends AbstractEntityFactory
{
    /**
     * @param array       $data
     * @param Game|null   $game
     * @param Board|null  $board
     * @param Player|null $player
     *
     * @return Game
     */
    public function make(array $data, ?Game $game = null, ?Board $board = null, Player $player = null): Game
    {
        if (is_null($game)) {
            $game = new Game();
        }

        $this->setEntityValue($game, $data);

        if (!is_null($board)) {
            $game->setBoard($board);
        }

        if (!is_null($player)) {
            $game->addPlayer($player);
        }

        return $game;
    }
}
