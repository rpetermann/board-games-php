<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Game;
use App\Entity\Player;
use App\Exception\GameException;
use App\Exception\PieceException;
use App\Exception\PlayerException;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * GameMovePieceModelTest
 */
class GameMovePieceModelTest extends AbstractWebTestCase
{
    const GAME_ADD_PLAYER_ROUTE = '/v1/game/%s/player/%s/piece/%s/move_piece';

    /**
     * @var GameRepository
     */
    protected $gameRepository;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->gameRepository = $this->em->getRepository(Game::class);
    }

    /**
     * dataProviderSucessfullyMovePieceCapturingPiece
     *
     * @return array
     */
    public function dataProviderSucessfullyMovePieceCapturingPiece(): array
    {
        return [
            [
                'playerIdx' => 0,
                'pieceIdx' => 1,
                'payload' => [
                    'toX' => 5,
                    'toY' => 5,
                ],
            ]
        ];
    }

    /**
     * testSucessfullyMovePieceCapturingPiece
     * 
     * @dataProvider dataProviderSucessfullyMovePieceCapturingPiece
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param array   $payload
     * @return void
     */
    public function testSucessfullyMovePieceCapturingPiece(int $playerIdx, int $pieceIdx, array $payload): void
    {
        $game = $this->createCheckersGameMockWithPiecesToCapture();
        $player = $game->getPlayer()[$playerIdx];
        $opponents = $game->getOpponents($player->getId());
        $piece = $player->getPieces()[$pieceIdx];
        $this->save($game);

        $countOpponentPiecesBeforeMovePiece = $this->countPlayersPieces(...$opponents);

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseIsSuccessful();

        $this->assertSame(Game::STATE_PLAYING, $game->getState());
        $this->assertSame(Player::STATE_WAITING_OPPONENT_PLAY, $player->getState());
        foreach ($opponents as $opponent) {
            $this->assertSame(Player::STATE_WAITING_PLAY, $opponent->getState());
            $this->assertNull($opponent->getPieceByPosition($payload['toX'], $payload['toY']));

            $countOpponentPieceAfterMove = $countOpponentPiecesBeforeMovePiece[$opponent->getId()] - 1;
            $this->assertSame($countOpponentPieceAfterMove, $opponent->countPieces());
        }

        $this->assertPieceMovedToPosition($piece, $payload['toX'], $payload['toY']);
        $this->assertHistoryExists(...$game->getHistory());
    }

    /**
     * dataProviderSucessfullyMovePieceWillFinishGame
     *
     * @return array
     */
    public function dataProviderSucessfullyMovePieceWillFinishGame(): array
    {
        return [
            [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'payload' => [
                    'toX' => 5,
                    'toY' => 5,
                ],
            ]
        ];
    }

    /**
     * testSucessfullyMovePieceWillFinishGame
     * 
     * @dataProvider dataProviderSucessfullyMovePieceWillFinishGame
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param array   $payload
     * @return void
     */
    public function testSucessfullyMovePieceWillFinishGame(int $playerIdx, int $pieceIdx, array $payload): void
    {
        $game = $this->createCheckersGameMockWithLastPieceToFinishGame();
        $player = $game->getPlayer()[$playerIdx];
        $opponents = $game->getOpponents($player->getId());
        $piece = $player->getPieces()[$pieceIdx];
        $this->save($game);

        $countOpponentPiecesBeforeMovePiece = $this->countPlayersPieces(...$opponents);

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNull($response->accessToken ?? null);

        $this->assertSame(Game::STATE_FINISHED, $game->getState());
        $this->assertSame(Player::STATE_WON, $player->getState());
        foreach ($opponents as $opponent) {
            $this->assertSame(Player::STATE_LOST, $opponent->getState());
            $this->assertNull($opponent->getPieceByPosition($payload['toX'], $payload['toY']));

            $countOpponentPieceAfterMove = $countOpponentPiecesBeforeMovePiece[$opponent->getId()] - 1;
            $this->assertSame($countOpponentPieceAfterMove, $opponent->countPieces());
        }

        $this->assertPieceMovedToPosition($piece, $payload['toX'], $payload['toY']);
        $this->assertHistoryExists(...$game->getHistory());
    }

    /**
     * dataProviderSucessfullyMovePieceWithoutCapturePiece
     *
     * @return array
     */
    public function dataProviderSucessfullyMovePieceWithoutCapturePiece(): array
    {
        return [
            [
                'playerIdx' => 0,
                'pieceIdx' => 2,
                'payload' => [
                    'toX' => 4,
                    'toY' => 6,
                ],
            ]
        ];
    }

    /**
     * testSucessfullyMovePieceWithoutCapturePiece
     * 
     * @dataProvider dataProviderSucessfullyMovePieceWithoutCapturePiece
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param array   $payload
     * @return void
     */
    public function testSucessfullyMovePieceWithoutCapturePiece(int $playerIdx, int $pieceIdx, array $payload): void
    {
        $game = $this->createCheckersGameMockWithPiecesToCapture();
        $player = $game->getPlayer()[$playerIdx];
        $opponents = $game->getOpponents($player->getId());
        $piece = $player->getPieces()[$pieceIdx];
        $this->save($game);

        $countOpponentPiecesBeforeMovePiece = $this->countPlayersPieces(...$opponents);

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNull($response->accessToken ?? null);

        $this->assertSame(Game::STATE_PLAYING, $game->getState());
        $this->assertSame(Player::STATE_WAITING_OPPONENT_PLAY, $player->getState());
        foreach ($opponents as $opponent) {
            $this->assertSame(Player::STATE_WAITING_PLAY, $opponent->getState());
            $this->assertSame($countOpponentPiecesBeforeMovePiece[$opponent->getId()], $opponent->countPieces());
        }

        $this->assertPieceMovedToPosition($piece, $payload['toX'], $payload['toY']);
        $this->assertHistoryExists(...$game->getHistory());
    }

    /**
     * testFailureNotFoundPieceToMove
     *
     * @return void
     */
    public function testFailureNotFoundPieceToMove(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player1 = $game->getPlayer()->first();
        $this->save($game);

        $payload = [
            'toX' => 1,
            'toY' => 1,
        ];

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player1->getId(), 'mocknonexistentpiece'),
            $payload
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(PieceException::TYPE_PIECE_NOT_FOUND, $response->message);
    }

    /**
     * testFailurePieceInOtherGame
     *
     * @return void
     */
    public function testFailurePieceInOtherGame(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player1 = $game->getPlayer()->first();
        $piece = $player1->getPieces()->first();
        $this->save($game);

        $payload = [
            'toX' => 1,
            'toY' => 1,
        ];

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute('mockothergameid', $player1->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(PieceException::TYPE_PIECE_NOT_FOUND, $response->message);
    }

    /**
     * testFailurePieceIsOwnedByOtherPlayer
     *
     * @return void
     */
    public function testFailurePieceIsOwnedByOtherPlayer(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player1 = $game->getPlayer()->first();
        $player2 = $game->getPlayer()[1];
        $player1Piece = $player1->getPieces()->first();
        $this->save($game);

        $payload = [
            'pieceId' => $player1Piece->getId(),
            'toX' => 1,
            'toY' => 1,
        ];

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player2->getId(), $player1Piece->getId()),
            $payload
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(PieceException::TYPE_PIECE_NOT_FOUND, $response->message);
    }

    /**
     * testFailureIsNotPlayerTurn
     *
     * @return void
     */
    public function testFailureIsNotPlayerTurn(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[1];
        $piece = $player->getPieces()->first();
        $this->save($game);

        $payload = [
            'toX' => 1,
            'toY' => 1,
        ];

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $response = $this->getJsonResponse();
        $this->assertSame(PlayerException::TYPE_IS_NOT_PLAYER_TURN, $response->message);
    }

    /**
     * dataProviderFailureUsingInvalidPayload
     *
     * @return array
     */
    public function dataProviderFailureUsingInvalidPayload(): array
    {
        return [
            'without Y data' => [
                'payload' =>  [
                    'toX' => 1,
                ],
            ],
            'without X data' => [
                'payload' =>  [
                    'toY' => 1,
                ],
            ],
            'without X and Y data' => [
                'payload' =>  [
                    'mock_data' => 1,
                ],
            ],
        ];
    }

    /**
     * testFailureUsingInvalidPayload
     * 
     * @dataProvider dataProviderFailureUsingInvalidPayload
     *
     * @return void
     */
    public function testFailureUsingInvalidPayload(array $payload): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $player = $game->getPlayer()[1];
        $piece = $player->getPieces()->first();
        $this->save($game);

        $this->sendRequest(
            'POST',
            $this->getMovePieceRoute($game->getId(), $player->getId(), $piece->getId()),
            $payload
        );
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->getJsonResponse();
        $this->assertSame(GameException::TYPE_INVALID_PAYLOAD, $response->message);
    }

    /**
     * getMovePieceRoute
     *
     * @param string $gameId
     * @param string $playerId
     * @param string $pieceId
     * @return string
     */
    protected function getMovePieceRoute(string $gameId, string $playerId, string $pieceId): string
    {
        return sprintf(self::GAME_ADD_PLAYER_ROUTE, $gameId, $playerId, $pieceId);
    }

    /**
     * countPlayersPieces
     *
     * @param Player ...$players
     * @return array
     */
    protected function countPlayersPieces(Player ...$players): array
    {
        $pieces = [];
        foreach ($players as $player) {
            $pieces[$player->getId()] = $player->countPieces();
        }

        return $pieces;
    }
}
