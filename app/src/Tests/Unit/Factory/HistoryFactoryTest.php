<?php

namespace App\Tests\Unit\Factory;

use App\Dto\HistoryDto;
use App\Entity\Game;
use App\Entity\History;
use App\Factory\HistoryFactory;
use App\Tests\Unit\AbstractUnitTest;

/**
 * HistoryFactoryTest
 */
class HistoryFactoryTest extends AbstractUnitTest
{
    /**
     * @var HistoryFactory
     */
    protected $historyFactory;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->historyFactory = new HistoryFactory();
    }

    /**
     * testMakePiece
     *
     * @return void
     */
    public function testMakeHistory(): void
    {
        $historyDto = new HistoryDto(
            new Game()
        );

        $response = $this->historyFactory->make($historyDto);

        $this->assertNotNull($response->getSnapshot()['game'] ?? null);
        $this->assertInstanceOf(History::class, $response);
    }
}
