<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Player;
use App\Entity\Game;
use App\Exception\GameException;
use App\Repository\GameRepository;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * GameStartModelTest
 */
class GameStartModelTest extends AbstractWebTestCase
{
    const GAME_START_ROUTE = '/v1/game/%s/start';

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
     * testSucessfullyStartGame
     *
     * @return void
     */
    public function testSucessfullyStartGame(): void
    {
        $game = $this->createCheckersGameMockWith2PlayersWaitingStart();
        $this->save($game);

        $headers = $this->getAccessTokenHeader($game->getAccessToken());

        $this->sendRequest('POST', $this->getStartGameRoute($game->getId()), [], $headers);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNull($response->accessToken ?? null);

        $this->assertHistoryExists(...$game->getHistory());

        $players = $game->getPlayer();
        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame(Game::STATE_PLAYING, $game->getState());
        foreach ($players as $player) {
            $this->assertInstanceOf(Player::class, $player);

            $expectedState = $player->isFirstPlayer() ? Player::STATE_WAITING_PLAY : Player::STATE_WAITING_OPPONENT_PLAY;
            $this->assertSame($expectedState, $player->getState());

            $expectedSequence = $this->getExpectedSequence(...$players);
            $playersSequence = $this->getPlayersSequence(...$players);
            $this->assertEqualsCanonicalizing($expectedSequence, $playersSequence);
        }
        $this->assertCount(2, $game->getPlayer());
        $this->assertNotNull($this->gameRepository->findById($game->getId()));
    }

    /**
     * testFailureStartGameUsingInvalidToken
     *
     * @return void
     */
    public function testFailureStartGameUsingInvalidToken(): void
    {
        $game = $this->createCheckersGameMockWith2PlayersWaitingStart();
        $this->save($game);

        $headers = $this->getAccessTokenHeader('test123');

        $this->sendRequest('POST', $this->getStartGameRoute($game->getId()), [], $headers);
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(GameException::TYPE_GAME_NOT_FOUND, $response->message);
    }

    /**
     * testFailureStartGameWithoutToken
     *
     * @return void
     */
    public function testFailureStartGameWithoutToken(): void
    {
        $game = $this->createCheckersGameMockWith2PlayersWaitingStart();
        $this->save($game);

        $this->sendRequest('POST', $this->getStartGameRoute($game->getId()));
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
    }

    /**
     * getStartGameRoute
     *
     * @param string $gameId
     * @return string
     */
    protected function getStartGameRoute(string $gameId): string
    {
        return sprintf(self::GAME_START_ROUTE, $gameId);
    }

    /**
     * getExpectedSequence
     *
     * @param Player ...$players
     * @return array
     */
    protected function getExpectedSequence(Player ...$players): array
    {
        return range(0, count($players) - 1);
    }

    /**
     * getPlayersSequence
     *
     * @param Player ...$players
     * @return array
     */
    protected function getPlayersSequence(Player ...$players): array
    {
        return array_map(function ($player) {
            return $player->getSequence();
        }, $players);
    }
}
