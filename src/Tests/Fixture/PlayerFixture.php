<?php

namespace App\Tests\Fixture;

use App\Entity\Player;

/**
 * PlayerFixture
 */
class PlayerFixture extends AbstractFixture
{
    /**
     * getMock
     *
     * @param  array $overrideData
     * @return Player
     */
    public static function getMock(array $overrideData = []): Player
    {
        $data = [
            'id' => self::generateUuid(),
            'name' => 'Mock Name',
            'sequence' => 0,
            'state' => Player::STATE_CREATING,
            'pieces' => [],
        ];

        $data = array_merge($data, $overrideData);

        $entity = new Player();
        self::setEntityProperties($entity, $data);

        return $entity;
    }
}
