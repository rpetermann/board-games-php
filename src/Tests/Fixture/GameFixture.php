<?php

namespace App\Tests\Fixture;

use App\Entity\Game;

/**
 * GameFixture
 */
class GameFixture extends AbstractFixture
{
    /**
     * getMock
     *
     * @param  array $overrideData
     * @return Game
     */
    public static function getMock(array $overrideData = []): Game
    {
        $data = [
            'id' => self::generateUuid(),
            'state' => Game::STATE_CREATING,
            'player' => [],
            'board' => null,
        ];

        $data = array_merge($data, $overrideData);

        $entity = new Game();
        self::setEntityProperties($entity, $data);

        return $entity;
    }
}
