<?php

namespace App\Tests\Fixture;

use App\Entity\Board;

/**
 * BoardFixture
 */
class BoardFixture extends AbstractFixture
{
    /**
     * getMock
     *
     * @param  array $overrideData
     * @return Board
     */
    public static function getMock(array $overrideData = []): Board
    {
        $data = [
            'id' => self::generateUuid(),
            'rows' => 0,
            'column' => 0,
            'type' => 'mock',
        ];

        $data = array_merge($data, $overrideData);

        $entity = new Board();
        self::setEntityProperties($entity, $data);

        return $entity;
    }
}
