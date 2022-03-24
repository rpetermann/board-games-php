<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Exception\GameException;
use App\Exception\PieceException;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

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

        $headers = $this->getAccessTokenHeader($game->getAccessToken());

        $this->sendRequest(
            'GET',
            $this->getAllowedPieceMovesRoute($game->getId(), $player->getId(), $piece->getId()),
            [],
            $headers,
        );
        $this->assertResponseIsSuccessful();

        $response = json_decode(json_encode($this->getJsonResponse()), true);
        $this->assertEquals($expectedResponse, $response);
    }

    /**
     * testFailureGetAllowedPieceMovesUsingInvalidToken
     *
     * @return void
     */
    public function testFailureGetAllowedPieceMovesUsingInvalidToken(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[0];
        $piece = $player->getPieces()[0];
        $this->save($game);

        $headers = $this->getAccessTokenHeader('imAnInvalidToken');

        $this->sendRequest(
            'GET',
            $this->getAllowedPieceMovesRoute($game->getId(), $player->getId(), $piece->getId()),
            [],
            $headers,
        );

        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(PieceException::TYPE_PIECE_NOT_FOUND, $response->message);
    }

    /**
     * testFailureGetAllowedPieceMovesWithoutToken
     *
     * @return void
     */
    public function testFailureGetAllowedPieceMovesWithoutToken(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[0];
        $piece = $player->getPieces()[0];
        $this->save($game);

        $this->sendRequest(
            'GET',
            $this->getAllowedPieceMovesRoute($game->getId(), $player->getId(), $piece->getId()),
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
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
