<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Board;
use App\Entity\Game;
use App\Entity\Player;
use App\Repository\GameRepository;
use App\Tests\Functional\AbstractWebTestCase;

/**
 * GameCreateModelTest
 */
class GameCreateModelTest extends AbstractWebTestCase
{
    const GAME_CREATE_GAME_ROUTE = '/v1/game';

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
     * dataProviderSucessfullyCreateGame
     *
     * @return array
     */
    public function dataProviderSucessfullyCreateGame(): array
    {
        return [
            'checkers' => [
                'payload' => [
                    'type' => 'checkers',
                ],
            ],
        ];
    }

    /**
     * testSucessfullyCreateGame
     * 
     * @dataProvider dataProviderSucessfullyCreateGame
     *
     * @param array $payload
     * @return void
     */
    public function testSucessfullyCreateGame(array $payload): void
    {
        $this->sendRequest('POST', $this->getCreateGameRoute(), $payload);
        $this->assertResponseIsSuccessful();

        $response = $this->getJsonResponse();

        $game = $this->gameRepository->findOneById($response->id);
        $this->assertNotEmpty($response->accessToken);
        $this->assertInstanceOf(Game::class, $game);
        $this->assertSame(Game::STATE_WAITING_PLAYERS, $game->getState());
        $this->assertInstanceOf(Board::class, $game->getBoard());
        $this->assertNotEmpty($game->getPlayer());
        $this->assertInstanceOf(Player::class, $game->getPlayer()[0]);
    }

    /**
     * getCreateGameRoute
     *
     * @return string
     */
    protected function getCreateGameRoute(): string
    {
        return sprintf(self::GAME_CREATE_GAME_ROUTE);
    }
}
