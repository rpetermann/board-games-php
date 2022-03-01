<?php

namespace App\Tests\Unit\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler;

use App\Tests\Unit\AbstractUnitTest;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDamePieceHandler;
use App\Tests\Common\MockTrait;

/**
 * CheckersDamePieceHandlerTest
 */
class CheckersDamePieceHandlerTest extends AbstractUnitTest
{
    use MockTrait;

    /**
     * @var CheckersDamePieceHandler
     */
    protected $checkersDamePieceHandler;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->checkersDamePieceHandler = new CheckersDamePieceHandler();
    }

    /**
     * dataProviderIsDirectionAllowed
     *
     * @return array
     */
    public function dataProviderIsDirectionAllowed(): array
    {
        return [
            'First Player - Piece on X = 0 and Y = 0 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 4,
                'toY' => 4,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 5,
                'toY' => 5,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 0,
                'toY' => 0,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 3 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 7,
                'toY' => 5,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 3 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 3,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 5 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 2,
                'toX' => 7,
                'toY' => 7,
                'expectedResponse' => true,
            ],
        ];
    }

    /**
     * testIsDirectionAllowed
     * 
     * @dataProvider dataProviderIsDirectionAllowed
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsDirectionAllowed(int $playerIdx, int $pieceIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersDamePieceHandler->isDirectionAllowed($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * dataProviderIsNumberOfStepsAllowed
     *
     * @return array
     */
    public function dataProviderIsNumberOfStepsAllowed(): array
    {
        return [
            'First Player - Piece on X = 0 and Y = 0 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 4,
                'toY' => 4,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 5,
                'toY' => 5,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 0,
                'toY' => 0,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 3 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 7,
                'toY' => 5,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 3 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 3,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 5 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 2,
                'toX' => 7,
                'toY' => 7,
                'expectedResponse' => true,
            ],
        ];
    }

    /**
     * testIsNumberOfStepsAllowed
     * 
     * @dataProvider dataProviderIsNumberOfStepsAllowed
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsNumberOfStepsAllowed(int $playerIdx, int $pieceIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersDamePieceHandler->isNumberOfStepsAllowed($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }
}
