<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Player;
use App\Factory\PlayerFactory;
use App\Tests\Unit\AbstractUnitTest;

/**
 * PlayerFactoryTest
 */
class PlayerFactoryTest extends AbstractUnitTest
{
    /**
     * @var PlayerFactory
     */
    protected $playerFactory;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->playerFactory = new PlayerFactory();
    }

    /**
     * dataProviderMakePlayerWillReturnDefaultName
     *
     * @return array
     */
    public function dataProviderMakePlayerWillReturnDefaultName(): array
    {
        return [
            'has not players in game' => [
                'payload' => [],
                'countPlayersInGame' => 0,
                'expectedName' => 'Player 1',
            ],
            'has 1 player in game' => [
                'payload' => [],
                'countPlayersInGame' => 1,
                'expectedName' => 'Player 2',
            ],
        ];
    }

    /**
     * testSucessfullyMakePlayerWillReturnDefaultName
     * 
     * @dataProvider dataProviderMakePlayerWillReturnDefaultName
     *
     * @param array   $payload
     * @param integer $countPlayersInGame
     * @param string  $expectedName
     * @return void
     */
    public function testSucessfullyMakePlayerWillReturnDefaultName(array $payload, int $countPlayersInGame, string $expectedName): void
    {
        $response = $this->playerFactory->make($payload, $countPlayersInGame);

        $this->assertSame($response->getName(), $expectedName);
        $this->assertInstanceOf(Player::class, $response);
    }
}
