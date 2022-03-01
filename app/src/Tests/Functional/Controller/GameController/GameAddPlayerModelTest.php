<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Player;
use App\Entity\Game;
use App\Exception\GameException;
use App\Repository\GameRepository;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * GameAddPlayerModelTest
 */
class GameAddPlayerModelTest extends AbstractWebTestCase
{
    const GAME_ADD_PLAYER_ROUTE = '/v1/game/%s/player';

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
     * testSucessfullyAddLastPlayerGame
     *
     * @return void
     */
    public function testSucessfullyAddLastPlayerGame(): void
    {
        $game = $this->createCheckersGameMockWith1Player();
        $this->save($game);

        $payload = [
            'name' => 'Player 2',
        ];

        $this->sendRequest('POST', $this->getAddPlayerRoute($game->getId()), $payload);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $this->assertNull($response->accessToken ?? null);

        $players = $game->getPlayer();
        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame(Game::STATE_WAITING_START, $game->getState());
        foreach ($players as $player) {
            $this->assertInstanceOf(Player::class, $player);
            $this->assertSame(Player::STATE_WAITING_START, $player->getState());
        }
        $this->assertCount(2, $game->getPlayer());
        $this->assertNotNull($this->gameRepository->findById($game->getId()));
    }

    /**
     * testFailureNumberMaximumOfPlayersWasReached
     *
     * @return void
     */
    public function testFailureNumberMaximumOfPlayersWasReached(): void
    {
        $game = $this->createCheckersGameMockWith2Players();        
        $this->save($game);

        $payload = [
            'name' => 'Player 3',
        ];

        $this->sendRequest('POST', $this->getAddPlayerRoute($game->getId()), $payload);
        $this->assertResponseStatusCodeSame(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response = $this->getJsonResponse();
        $this->assertSame(GameException::TYPE_GAME_REACHED_MAXIMUM_NUMBER_OF_PLAYERS, $response->message);
    }

    /**
     * getAddPlayereRoute
     *
     * @param string $gameId
     * @return string
     */
    protected function getAddPlayerRoute(string $gameId): string
    {
        return sprintf(self::GAME_ADD_PLAYER_ROUTE, $gameId);
    }
}
