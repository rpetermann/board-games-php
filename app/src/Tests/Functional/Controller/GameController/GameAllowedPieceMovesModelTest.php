<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Tests\Functional\AbstractWebTestCase;

/**
 * GameAllowedPieceMovesModelTest
 */
class GameAllowedPieceMovesModelTest extends AbstractWebTestCase
{
    const GAME_ALLOWED_PIECE_MOVES_ROUTE = '/v1/game/%s/player/%s/piece/%s/allowed_moves';

    /**
     * dataProviderSucessfullyGetAllowedPieceMoves
     *
     * @return array
     */
    public function dataProviderSucessfullyGetAllowedPieceMoves(): array
    {
        return [
            'without moves allowed' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'expectedResponse' => [],
            ],
            '2 moves allowed' => [
                'playerIdx' => 0,
                'pieceIdx' => 9,
                'expectedResponse' => [
                    [
                        'x' => 3,
                        'y' => 1,
                    ],
                    [
                        'x' => 3,
                        'y' => 3,
                    ],
                ],
            ],
        ];
    }

    /**
     * testSucessfullyGetAllowedPieceMoves
     * 
     * @dataProvider dataProviderSucessfullyGetAllowedPieceMoves
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param array   $expectedResponse
     * @return void
     */
    public function testSucessfullyGetAllowedPieceMoves(int $playerIdx, int $pieceIdx, array $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];
        $this->save($game);

        $this->sendRequest(
            'GET',
            $this->getAllowedPieceMovesRoute($game->getId(), $player->getId(), $piece->getId())
        );
        $this->assertResponseIsSuccessful();

        $response = json_decode(json_encode($this->getJsonResponse()), true);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * getAllowedPieceMovesRoute
     *
     * @param string $gameId
     * @param string $playerId
     * @param string $pieceId
     * @return string
     */
    protected function getAllowedPieceMovesRoute(string $gameId, string $playerId, string $pieceId): string
    {
        return sprintf(self::GAME_ALLOWED_PIECE_MOVES_ROUTE, $gameId, $playerId, $pieceId);
    }

}
