<?php

namespace App\Tests\Unit\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler;

use App\Dto\SlotDto;
use App\Entity\Game;
use App\Tests\Unit\AbstractUnitTest;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;
use App\Tests\Common\MockTrait;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * CheckersDefaultPieceHandlerTest
 */
class CheckersDefaultPieceHandlerTest extends AbstractUnitTest
{
    use MockTrait;

    /**
     * @var CheckersDefaultPieceHandler
     */
    protected $checkersDefaultPieceHandler;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->checkersDefaultPieceHandler = new CheckersDefaultPieceHandler();
    }

    /**
     * dataProviderIsDestinationUsingCurrentPosition
     *
     * @return array
     */
    public function dataProviderIsDestinationUsingCurrentPosition(): array
    {
        return [
            'true = position x=0 and y=0' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 0,
                'toY' => 0,
                'expectedResponse' => true,
            ],
            'false = position x=1 and y=1' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 1,
                'toY' => 1,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testIsDestinationUsingCurrentPosition
     * 
     * @dataProvider dataProviderIsDestinationUsingCurrentPosition
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsDestinationUsingCurrentPosition(int $playerIdx, int $pieceIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersDefaultPieceHandler->IsDestinationUsingCurrentPosition($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }


    /**
     * dataProviderIsSlotOccupied
     *
     * @return array
     */
    public function dataProviderIsSlotOccupied(): array
    {
        return [
            'occupied' => [
                'x' => 0,
                'y' => 0,
                'expectedResponse' => true,
            ],
            'not occupied' => [
                'x' => 3,
                'y' => 1,
                'expectedResponse' => false,
            ],
            'occupied by player2 piece' => [
                'x' => 0,
                'y' => 7,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testIsSlotOccupied
     * 
     * @dataProvider dataProviderIsSlotOccupied
     *
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsSlotOccupied(int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()->first();
        $piece = $player->getPieces()[0];

        $response = $this->checkersDefaultPieceHandler->isSlotOccupied($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * dataProviderIsPositionOutOfBoardRange
     *
     * @return array
     */
    public function dataProviderIsPositionOutOfBoardRange(): array
    {
        return [
            'out of range = false' => [
                'x' => 0,
                'y' => 0,
                'expectedResponse' => false,
            ],
            'out of range using negative numbers = true' => [
                'x' => -1,
                'y' => -3,
                'expectedResponse' => true,
            ],
            'out of range using minimum X value = false' => [
                'x' => 0,
                'y' => 3,
                'expectedResponse' => false,
            ],
            'out of range using maximum X value  = false' => [
                'x' => 7,
                'y' => 3,
                'expectedResponse' => false,
            ],
            'out of range using minimum Y value = false' => [
                'x' => 3,
                'y' => 0,
                'expectedResponse' => false,
            ],
            'out of range using maximum Y value  = false' => [
                'x' => 3,
                'y' => 7,
                'expectedResponse' => false,
            ],
            'out of range using minimum X and Y value = false' => [
                'x' => 0,
                'y' => 0,
                'expectedResponse' => false,
            ],
            'out of range using maximum X and Y value  = false' => [
                'x' => 7,
                'y' => 7,
                'expectedResponse' => false,
            ],
            'out of range using on X = true' => [
                'x' => 8,
                'y' => 7,
                'expectedResponse' => true,
            ],
            'out of range using on Y = true' => [
                'x' => 3,
                'y' => 8,
                'expectedResponse' => true,
            ],
            'out of range using on X and Y = true' => [
                'x' => 8,
                'y' => 8,
                'expectedResponse' => true,
            ],
        ];
    }

    /**
     * testIsPositionOutOfBoardRange
     * 
     * @dataProvider dataProviderIsPositionOutOfBoardRange
     *
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsPositionOutOfBoardRange(int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()->first();
        $piece = $player->getPieces()[0];

        $response = $this->checkersDefaultPieceHandler->isPositionOutOfBoardRange($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * dataProviderIsDiagonalMove
     *
     * @return array
     */
    public function dataProviderIsDiagonalMove(): array
    {
        return [
            '0 First Player - diagonal' => [
                'playerIdx' => 0,
                'x' => 1,
                'y' => 1,
                'expectedResponse' => true,
            ],
            '1 First Player - diagonal' => [
                'playerIdx' => 0,
                'x' => 4,
                'y' => 4,
                'expectedResponse' => true,
            ],
            '2 First Player - diagonal' => [
                'playerIdx' => 0,
                'x' => 6,
                'y' => 6,
                'expectedResponse' => true,
            ],
            '3 First Player - non diagonal' => [
                'playerIdx' => 0,
                'x' => 0,
                'y' => 1,
                'expectedResponse' => false,
            ],
            '4 First Player - non diagonal' => [
                'playerIdx' => 0,
                'x' => 3,
                'y' => 2,
                'expectedResponse' => false,
            ],
            '5 First Player - non diagonal' => [
                'playerIdx' => 0,
                'x' => 1,
                'y' => 2,
                'expectedResponse' => false,
            ],
            '0 Second Player - diagonal' => [
                'playerIdx' => 1,
                'x' => 6,
                'y' => 2,
                'expectedResponse' => true,
            ],
            '1 Second Player - diagonal' => [
                'playerIdx' => 1,
                'x' => 7,
                'y' => 3,
                'expectedResponse' => true,
            ],
            '2 Second Player - diagonal' => [
                'playerIdx' => 1,
                'x' => 4,
                'y' => 0,
                'expectedResponse' => true,
            ],
            '3 Second Player - non diagonal' => [
                'playerIdx' => 1,
                'x' => 0,
                'y' => 1,
                'expectedResponse' => false,
            ],
            '4 Second Player - non diagonal' => [
                'playerIdx' => 1,
                'x' => 7,
                'y' => 0,
                'expectedResponse' => false,
            ],
            '5 Second Player - non diagonal' => [
                'playerIdx' => 1,
                'x' => 2,
                'y' => 3,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testIsDiagonalMove
     * 
     * @dataProvider dataProviderIsDiagonalMove
     *
     * @param integer $playerIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testIsDiagonalMove(int $playerIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[0];

        $response = $this->checkersDefaultPieceHandler->isDiagonalMove($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
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
                'toX' => 1,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 0 and Y = 4 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 2,
                'toX' => 1,
                'toY' => 5,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 6 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 11,
                'toX' => 3,
                'toY' => 7,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 1,
                'toY' => 1,
                'expectedResponse' => false,
            ],
            'First Player - Piece on X = 1 and Y = 3 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 5,
                'toX' => 0,
                'toY' => 2,
                'expectedResponse' => false,
            ],
            'First Player - Piece on X = 2 and Y = 0 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 8,
                'toX' => 1,
                'toY' => 1,
                'expectedResponse' => false,
            ],
            'Second Player - Piece on X = 5 and Y = 0 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 0,
                'toX' => 4,
                'toY' => 0,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 0 and Y = 7 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 8,
                'toX' => 6,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 6 and Y = 5 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 6,
                'toX' => 5,
                'toY' => 6,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 2 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 6,
                'toY' => 1,
                'expectedResponse' => false,
            ],
            'Second Player - Piece on X = 5 and Y = 6 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 3,
                'toX' => 6,
                'toY' => 7,
                'expectedResponse' => false,
            ],
            'Second Player - Piece on X = 6 and Y = 5 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 6,
                'toX' => 7,
                'toY' => 6,
                'expectedResponse' => false,
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

        $response = $this->checkersDefaultPieceHandler->isDirectionAllowed($piece, $toX, $toY);

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
                'toX' => 1,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 3,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 3,
                'toY' => 3,
                'expectedResponse' => true,
            ],
            'First Player - Piece on X = 2 and Y = 2 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'toX' => 4,
                'toY' => 4,
                'expectedResponse' => false,
            ],
            'First Player - Piece on X = 2 and Y = 4 position' => [
                'playerIdx' => 0,
                'pieceIdx' => 10,
                'toX' => 5,
                'toY' => 1,
                'expectedResponse' => false,
            ],
            'Second Player - Piece on X = 5 and Y = 2 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 4,
                'toY' => 1,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 2 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'toX' => 4,
                'toY' => 3,
                'expectedResponse' => true,
            ],
            'Second Player - Piece on X = 5 and Y = 2 position' => [
                'playerIdx' => 1,
                'pieceIdx' => 3,
                'toX' => 3,
                'toY' => 4,
                'expectedResponse' => false,
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

        $response = $this->checkersDefaultPieceHandler->isNumberOfStepsAllowed($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * dataProviderHasPieceProtectingJump
     *
     * @return array
     */
    public function dataProviderHasPieceProtectingJump(): array
    {
        return [
            'There is an opponent piece protecting on position X=5 and Y=3' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 4,
                'toY' => 2,
                'expectedResponse' => true,
            ],
            'There is not an opponent piece protecting' => [
                'playerIdx' => 0,
                'pieceIdx' => 2,
                'toX' => 4,
                'toY' => 6,
                'expectedResponse' => false,
            ],
        ];
    }

    /**
     * testHasPieceProtectingJump
     * 
     * @dataProvider dataProviderHasPieceProtectingJump
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testHasPieceProtectingJump(int $playerIdx, int $pieceIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWithPiecesToCapture();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersDefaultPieceHandler->hasPieceProtectingJump($piece, $toX, $toY, ...$this->getCheckersBoardSlots());

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * dataProviderGetAllowedPieceMoves
     *
     * @return array
     */
    public function dataProviderGetAllowedPieceMoves(): array
    {
        return [
            'without moves' => [
                'game' => $this->createCheckersGameMockWith2Players(),
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'expectedResponse' => []
            ],
            'can be moved to x = 4 and y = 2 AND x = 4 and y = 4' => [
                'game' => $this->createCheckersGameMockWith2Players(),
                'playerIdx' => 1,
                'pieceIdx' => 1,
                'expectedResponse' => [
                    new SlotDto(4, 2),
                    new SlotDto(4, 4),
                ]
            ],
            'can be moved to x = 4 and y = 0' => [
                'game' => $this->createCheckersGameMockWithPiecesToCapture(),
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'expectedResponse' => [
                    new SlotDto(4, 0),
                ]
            ],
        ];
    }

    /**
     * testGetAllowedPieceMoves
     * 
     * @dataProvider dataProviderGetAllowedPieceMoves
     *
     * @param Game    $game
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param array   $expectedResponse
     * @return void
     */
    public function testGetAllowedPieceMoves(Game $game, int $playerIdx, int $pieceIdx, array $expectedResponse): void
    {
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersDefaultPieceHandler->getAllowedPieceMoves($piece, ...$this->getCheckersBoardSlots());

        $this->assertEquals(new ArrayCollection($expectedResponse), $response);
    }
}
