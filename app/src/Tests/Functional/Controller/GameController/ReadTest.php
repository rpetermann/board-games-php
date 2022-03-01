<?php

namespace App\Tests\Functional\Controller\GameController;

use App\Entity\Game;
use App\Exception\GameException;
use App\Tests\Functional\AbstractWebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * ReadTest
 */
class ReadTest extends AbstractWebTestCase
{
    const GAME_READ_ROUTE = '/v1/game/%s';

    /**
     * dataProviderSucessfullyRead
     *
     * @return array
     */
    public function dataProviderSucessfullyRead(): array
    {
        return [
            [
                'game' => $this->createCheckersGameMockWith2Players(),
            ],
        ];
    }

    /**
     * testSucessfullyRead
     * 
     * @dataProvider dataProviderSucessfullyRead
     *
     * @param Game $game
     * @return void
     */
    public function testSucessfullyRead(Game $game): void
    {
        $this->save($game);

        $this->sendRequest('GET', $this->getReadGameRoute($game->getId()));
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $this->getJsonResponse();
        $this->assertNotNull($response);
        $this->assertNotNull($response->player);
        $this->assertNull($response->accessToken ?? null);
    }

    public function dataProviderFailureRead(): array
    {
        return [
            'game not found' => [
                'gameId' => '56450b7d-330d-444e-8d66-e07e198c2b8e',
                'expectedResponse' => GameException::TYPE_GAME_NOT_FOUND,
                'expectedStatusCode' => Response::HTTP_NOT_FOUND,
            ],
        ];
    }

    /**
     * testFailureRead
     * 
     * @dataProvider dataProviderFailureRead
     *
     * @param string  $gameId
     * @param string  $expectedResponse
     * @param int     $expectedStatusCode
     * @return void
     */
    public function testFailureRead(string $gameId, string $expectedResponse, int $expectedStatusCode): void
    {
        $this->sendRequest('GET', $this->getReadGameRoute($gameId));

        $this->assertResponseStatusCodeSame($expectedStatusCode);
        $response = $this->getJsonResponse();
        $this->assertSame(GameException::TYPE_GAME_NOT_FOUND, $response->message);
    }

    /**
     * getReadGameRoute
     *
     * @param string $gameId
     * @return string
     */
    protected function getReadGameRoute(string $gameId): string
    {
        return sprintf(self::GAME_READ_ROUTE, $gameId);
    }
}
