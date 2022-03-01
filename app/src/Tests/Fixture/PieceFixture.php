<?php

namespace App\Tests\Fixture;

use App\Entity\Piece;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * PieceFixture
 */
class PieceFixture extends AbstractFixture
{
    /**
     * getMock
     *
     * @param  array $overrideData
     * @return Piece
     */
    public static function getMock(array $overrideData = []): Piece
    {
        $data = [
            'id' => self::generateUuid(),
            'x' => 0,
            'y' => 0,
            'type' => CheckersDefaultPieceHandler::TYPE,
        ];

        $data = array_merge($data, $overrideData);

        $entity = new Piece();
        self::setEntityProperties($entity, $data);

        return $entity;
    }

    /**
     * getDefaultCheckersPieces
     *
     * @return Collection
     */
    public static function getDefaultCheckersPieces(): Collection
    {
        return new ArrayCollection(
            array_merge(
                self::getDefaultPlayer1Pieces()->toArray(),
                self::getDefaultPlayer2Pieces()->toArray()
            )
        );
    }

    /**
     * getDefaultPlayer1Pieces
     *
     * @return Collection<i, Piece>
     */
    public static function getDefaultPlayer1Pieces(): Collection
    {
        return new ArrayCollection([
            PieceFixture::getMock(['x' => 0, 'y' => 0, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 0, 'y' => 2, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 0, 'y' => 4, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 0, 'y' => 6, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 1, 'y' => 1, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 1, 'y' => 3, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 1, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 1, 'y' => 7, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 2, 'y' => 0, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 2, 'y' => 2, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 2, 'y' => 4, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 2, 'y' => 6, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ]);
    }

    /**
     * getDefaultPlayer2Pieces
     *
     * @return Collection
     */
    public static function getDefaultPlayer2Pieces(): Collection
    {
        return new ArrayCollection([
            PieceFixture::getMock(['x' => 5, 'y' => 1, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 5, 'y' => 3, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 5, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 5, 'y' => 7, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 6, 'y' => 0, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 6, 'y' => 2, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 6, 'y' => 4, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 6, 'y' => 6, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 7, 'y' => 1, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 7, 'y' => 3, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 7, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 7, 'y' => 7, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ]);
    }
}
