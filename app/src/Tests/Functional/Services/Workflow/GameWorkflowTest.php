<?php

namespace App\Tests\Functional\Services\Workflow;

use App\Entity\Game;
use App\Services\Workflow\Exception\WorkflowException;
use App\Services\Workflow\WorkflowService;
use App\Tests\Fixture\GameFixture;
use App\Tests\Functional\AbstractWebTestCase;

/**
 * GameWorkflowTest
 */
class GameWorkflowTest extends AbstractWebTestCase
{
    /**
     * @var WorkflowService
     */
    protected $workflow;

    /**
     * setUp
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->workflow = $this->getContainer()->get(WorkflowService::class);
    }

    /**
     * dataProviderSucessfullyChangeGameStatus
     *
     * @return array
     */
    public function dataProviderSucessfullyChangeGameStatus(): array
    {
        return [
            'from creating to waiting_players' => [
                'from' => Game::STATE_CREATING,
                'to' => Game::TRANSITION_WAITING_PLAYERS,
                'expectedState' => Game::STATE_WAITING_PLAYERS,
            ],
            'from waiting_players to waiting_start' => [
                'from' => Game::STATE_WAITING_PLAYERS,
                'to' => Game::TRANSITION_WAITING_START,
                'expectedState' => Game::STATE_WAITING_START,
            ],
            'from waiting_start to playing' => [
                'from' => Game::STATE_WAITING_START,
                'to' => Game::TRANSITION_PLAYING,
                'expectedState' => Game::STATE_PLAYING,
            ],
            'from playing to finished' => [
                'from' => Game::STATE_PLAYING,
                'to' => Game::TRANSITION_FINISHED,
                'expectedState' => Game::STATE_FINISHED,
            ],
        ];
    }

    /**
     * testSucessfullyChangeGameStatus
     * 
     * @dataProvider dataProviderSucessfullyChangeGameStatus
     *
     * @param string $from
     * @param string $to
     * @param string $expectedState
     * @return void
     */
    public function testSucessfullyChangeGameStatus(string $from, string $to, string $expectedState): void
    {
        $game = GameFixture::getMock([
            'state' => $from,
        ]);

        $this->workflow->changeStatus($to, $game);

        $this->assertSame($game->getState(), $expectedState);
    }

    /**
     * dataProviderFailureChangeGameStatus
     *
     * @return array
     */
    public function dataProviderFailureChangeGameStatus(): array
    {
        return [
            'from creating to playing' => [
                'from' => Game::STATE_CREATING,
                'to' => Game::TRANSITION_PLAYING,
                'expectedState' => Game::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_playing" cannot be applied from state "creating"',
            ],
            'from creating to finished' => [
                'from' => Game::STATE_CREATING,
                'to' => Game::TRANSITION_FINISHED,
                'expectedState' => Game::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_finished" cannot be applied from state "creating"',
            ],
            'from waiting_players to playing' => [
                'from' => Game::STATE_WAITING_PLAYERS,
                'to' => Game::TRANSITION_PLAYING,
                'expectedState' => Game::STATE_WAITING_PLAYERS,
                'expectedExceptionMessage' => 'Transition "send_to_playing" cannot be applied from state "waiting_players"',
            ],
            'from waiting_players to finished' => [
                'from' => Game::STATE_WAITING_PLAYERS,
                'to' => Game::TRANSITION_FINISHED,
                'expectedState' => Game::STATE_WAITING_PLAYERS,
                'expectedExceptionMessage' => 'Transition "send_to_finished" cannot be applied from state "waiting_players"',
            ],
        ];
    }

    /**
     * testFailureChangeGameStatus
     * 
     * @dataProvider dataProviderFailureChangeGameStatus
     *
     * @param string $from
     * @param string $to
     * @param string $expectedState
     * @param string $expectedExceptionMessage
     * @return void
     */
    public function testFailureChangeGameStatus(string $from, string $to, string $expectedState, string $expectedExceptionMessage): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $game = GameFixture::getMock([
            'state' => $from,
        ]);

        $this->workflow->changeStatus($to, $game);
    }
}
