<?php

namespace App\Factory;

use App\Entity\Player;

/**
 * PlayerFactory
 */
class PlayerFactory extends AbstractEntityFactory
{
    const DEFAULT_PLAYER_NAME = 'Player %s';

    /**
     * @param array       $data
     * @param integer     $countPlayersInGame
     * @param Player|null $player
     *
     * @return Player
     */
    public function make(array $data, int $countPlayersInGame, ?Player $player = null): Player
    {
        if (is_null($player)) {
            $player = new Player();
        }

        $data['name'] = $this->getDefaultPlayerName($countPlayersInGame);

        $this->setEntityValue($player, $data);

        return $player;
    }

    /**
     * getDefaultPlayerName
     *
     * @param integer $countPlayersInGame
     * @return string
     */
    public function getDefaultPlayerName(int $countPlayersInGame): string
    {
        $playerNumber = $countPlayersInGame + 1;

        return sprintf(self::DEFAULT_PLAYER_NAME, $playerNumber);
    }
}
