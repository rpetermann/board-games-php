<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Piece;
use App\Factory\PieceFactory;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;
use App\Tests\Unit\AbstractUnitTest;

/**
 * PieceFactoryTest
 */
class PieceFactoryTest extends AbstractUnitTest
{
    /**
     * @var PieceFactory
     */
    protected $pieceFactory;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->pieceFactory = new PieceFactory();
    }

    /**
     * dataProviderMakePiece
     *
     * @return array
     */
    public function dataProviderMakePiece(): array
    {
        return [
            'default checkers piece' => [
                'payload' => [
                    'x' => 0,
                    'y' => 0,
                    'type' => CheckersDefaultPieceHandler::TYPE
                ],
                'expectedType' => 'default',
            ],
        ];
    }

    /**
     * testMakePiece
     * 
     * @dataProvider dataProviderMakePiece
     *
     * @param array  $payload
     * @param string $expectedType
     * @return void
     */
    public function testMakePiece(array $payload, string $expectedType): void
    {
        $response = $this->pieceFactory->make($payload);

        $this->assertNotNull($response->getX());
        $this->assertNotNull($response->getY());
        $this->assertSame($expectedType, $response->getType());
        $this->assertInstanceOf(Piece::class, $response);
    }
}
