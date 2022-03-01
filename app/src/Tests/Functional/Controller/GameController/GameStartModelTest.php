<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Player;
use App\Entity\Game;
use App\Repository\GameRepository;
use App\Tests\Functional\AbstractWebTestCase;

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

        $this->sendRequest('POST', $this->getStartGameRoute($game->getId()));
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
