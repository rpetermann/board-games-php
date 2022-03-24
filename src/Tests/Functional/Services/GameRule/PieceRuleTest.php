<?php

namespace App\Tests\Functional\Services\GameRule;

use App\Services\GameRule\Exception\PieceRuleException;
use App\Services\GameRule\PieceRule;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDamePieceHandler;
use App\Tests\Functional\AbstractWebTestCase;

/**
 * PieceRuleTest
 */
class PieceRuleTest extends AbstractWebTestCase
{
    /**
     * @var PieceRule
     */
    protected $pieceRule;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->pieceRule = $this->getContainer()->get(PieceRule::class);
    }

    /**
     * dataProviderSucessfullyGetHandlerByType
     *
     * @return array
     */
    public function dataProviderSucessfullyGetHandlerByType(): array
    {
        return [
            'checkers default piece rule' => [
                'pieceType' => 'default',
                'gameType' => 'checkers',
                'expectedResponse' => CheckersDefaultPieceHandler::class,
            ],
            'checkers dame piece rule' => [
                'pieceType' => 'dame',
                'gameType' => 'checkers',
                'expectedResponse' => CheckersDamePieceHandler::class,
            ],
        ];
    }

    /**
     * testSucessfullyGetHandlerByType
     * 
     * @dataProvider dataProviderSucessfullyGetHandlerByType
     *
     * @param string $pieceType
     * @param string $gameType
     * @param string $expectedResponse
     * @return void
     */
    public function testSucessfullyGetHandlerByType(string $pieceType, string $gameType, string $expectedResponse): void
    {
        $handler = $this->pieceRule->getHandlerByType($pieceType, $gameType);

        $this->assertSame($expectedResponse, get_class($handler));
    }

    /**
     * dataProviderFailureGetHandlerByType
     *
     * @return array
     */
    public function dataProviderFailureGetHandlerByType(): array
    {
        return [
            'invalid piece type' => [
                'pieceType' => 'xyz',
                'gameType' => 'checkers',
            ],
            'invalid game type' => [
                'pieceType' => 'dame',
                'gameType' => 'mockgame',
            ],
        ];
    }

    /**
     * testFailureGetHandlerByType
     * 
     * @dataProvider dataProviderFailureGetHandlerByType
     *
     * @param string $pieceType
     * @param string $gameType
     * @return void
     */
    public function testFailureGetHandlerByType(string $pieceType, string $gameType): void
    {
        $this->expectException(PieceRuleException::class);
        $this->expectExceptionMessage(PieceRuleException::TYPE_HANDLER_NOT_FOUND);

        $this->pieceRule->getHandlerByType($pieceType, $gameType);
    }
}
