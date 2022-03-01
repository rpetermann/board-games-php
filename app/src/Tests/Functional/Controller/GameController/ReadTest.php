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

        $headers = $this->getAccessTokenHeader($game->getAccessToken());

        $this->sendRequest('GET', $this->getReadGameRoute($game->getId()), [], $headers);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $response = $this->getJsonResponse();
        $this->assertNotNull($response);
        $this->assertNotNull($response->player);
        $this->assertNull($response->accessToken ?? null);
    }

    /**
     * testFailureReadUsingInvalidToken
     *
     * @return void
     */
    public function testFailureReadUsingInvalidToken(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $this->save($game);

        $headers = $this->getAccessTokenHeader('imAnInvalidToken');

        $this->sendRequest('GET', $this->getReadGameRoute($game->getId()), [], $headers);
        
        $this->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
        $response = $this->getJsonResponse();
        $this->assertSame(GameException::TYPE_GAME_NOT_FOUND, $response->message);
    }

    /**
     * testFailureReadWithoutToken
     *
     * @return void
     */
    public function testFailureReadWithoutToken(): void
    {
        $game = $this->createCheckersGameMockWith2Players();
        $this->save($game);

        $this->sendRequest('GET', $this->getReadGameRoute($game->getId()));
        
        $this->assertResponseStatusCodeSame(Response::HTTP_FORBIDDEN);
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
