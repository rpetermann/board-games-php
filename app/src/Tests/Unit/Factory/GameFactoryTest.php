<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Game;
use App\Factory\GameFactory;
use App\Tests\Unit\AbstractUnitTest;

/**
 * GameFactoryTest
 */
class GameFactoryTest extends AbstractUnitTest
{
    /**
     * @var GameFactory
     */
    protected $gameFactory;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->gameFactory = new GameFactory();
    }

    /**
     * dataProviderSucessfullyMakeGame
     *
     * @return array
     */
    public function dataProviderSucessfullyMakeGame(): array
    {
        return [
            'make Game' => [
                'payload' => [],
            ],
        ];
    }

    /**
     * testSucessfullyMakeGame
     * 
     * @dataProvider dataProviderSucessfullyMakeGame
     *
     * @param string $type
     * @return void
     */
    public function testSucessfullyMakeGame(array $payload): void
    {
        $response = $this->gameFactory->make($payload);

        $this->assertNotEmpty($response->getAccessToken());
        $this->assertInstanceOf(Game::class, $response);
    }
}
