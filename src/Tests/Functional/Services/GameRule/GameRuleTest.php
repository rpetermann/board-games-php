<?php

namespace App\Tests\Functional\Services\GameRule;

use App\Services\GameRule\Exception\GameRuleException;
use App\Services\GameRule\GameRule;
use App\Services\GameRule\RuleHandler\Checkers\CheckersHandler;
use App\Tests\Functional\AbstractWebTestCase;

/**
 * GameRuleTest
 */
class GameRuleTest extends AbstractWebTestCase
{
    /**
     * @var GameRule
     */
    protected $gameRule;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->gameRule = $this->getContainer()->get(GameRule::class);
    }

    /**
     * dataProviderSucessfullyGetHandlerByType
     *
     * @return array
     */
    public function dataProviderSucessfullyGetHandlerByType(): array
    {
        return [
            'checkers rule' => [
                'type' => 'checkers',
                'expectedResponse' => CheckersHandler::class,
            ],
        ];
    }

    /**
     * testSucessfullyGetHandlerByType
     * 
     * @dataProvider dataProviderSucessfullyGetHandlerByType
     *
     * @param string $type
     * @param string $expectedResponse
     * @return void
     */
    public function testSucessfullyGetHandlerByType(string $type, string $expectedResponse): void
    {
        $handler = $this->gameRule->getHandlerByType($type);

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
            'mock' => [
                'type' => 'mockxyz'
            ],
        ];
    }

    /**
     * testFailureGetHandlerByType
     * 
     * @dataProvider dataProviderFailureGetHandlerByType
     *
     * @param string $type
     * @return void
     */
    public function testFailureGetHandlerByType(string $type): void
    {
        $this->expectException(GameRuleException::class);
        $this->expectExceptionMessage(GameRuleException::TYPE_HANDLER_NOT_FOUND);

        $this->gameRule->getHandlerByType($type);
    }
}
