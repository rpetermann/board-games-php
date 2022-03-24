<?php

namespace App\Tests\Unit\Factory;

use App\Entity\Board;
use App\Factory\BoardFactory;
use App\Factory\PieceFactory;
use App\Services\GameRule\GameRule;
use App\Services\GameRule\PieceRule;
use App\Services\GameRule\RuleHandler\Checkers\CheckersHandler;
use App\Tests\Unit\AbstractUnitTest;

/**
 * BoardFactoryTest
 */
class BoardFactoryTest extends AbstractUnitTest
{
    /**
     * @var BoardFactory
     */
    protected $boardFactory;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $gameRule = new GameRule(
            new PieceRule
        );
        $gameRule->addHandler(new CheckersHandler(
            new PieceRule(),
            new PieceFactory()
        ));

        $this->boardFactory = new BoardFactory(
            $gameRule
        );
    }

    /**
     * dataProviderSucessfullyMakeBoardByType
     *
     * @return array
     */
    public function dataProviderSucessfullyMakeBoardByType(): array
    {
        return [
            'make Board by type' => [
                'type' => 'checkers',
            ],
        ];
    }

    /**
     * testSucessfullyMakeBoardByType
     * 
     * @dataProvider dataProviderSucessfullyMakeBoardByType
     *
     * @param string $type
     * @return void
     */
    public function testSucessfullyMakeBoardByType(string $type): void
    {
        $response = $this->boardFactory->makeByType($type);

        $this->assertSame(8, $response->getRows());
        $this->assertSame(8, $response->getColumns());
        $this->assertSame('checkers', $response->getType());
        $this->assertInstanceOf(Board::class, $response);
    }
}
