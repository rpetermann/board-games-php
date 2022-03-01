<?php

namespace App\Tests\Functional\Services\Workflow;

use App\Entity\Player;
use App\Services\Workflow\Exception\WorkflowException;
use App\Services\Workflow\WorkflowService;
use App\Tests\Fixture\PlayerFixture;
use App\Tests\Functional\AbstractWebTestCase;

/**
 * PlayerWorkflowTest
 */
class PlayerWorkflowTest extends AbstractWebTestCase
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
     * dataProviderSucessfullyChangePlayerStatus
     *
     * @return array
     */
    public function dataProviderSucessfullyChangePlayerStatus(): array
    {
        return [
            'from creating to waiting_start' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_WAITING_START,
                'expectedState' => Player::STATE_WAITING_START,
            ],
            'from waiting_start to waiting_play' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_WAITING_PLAY,
                'expectedState' => Player::STATE_WAITING_PLAY,
            ],
            'from waiting_start to waiting_opponent_play' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_WAITING_OPPONENT_PLAY,
                'expectedState' => Player::STATE_WAITING_OPPONENT_PLAY,
            ],
            'from waiting_play to waiting_opponent_play' => [
                'from' => Player::STATE_WAITING_PLAY,
                'to' => Player::TRANSITION_WAITING_OPPONENT_PLAY,
                'expectedState' => Player::STATE_WAITING_OPPONENT_PLAY,
            ],
            'from waiting_opponent_play to waiting_play' => [
                'from' => Player::STATE_WAITING_OPPONENT_PLAY,
                'to' => Player::TRANSITION_WAITING_PLAY,
                'expectedState' => Player::STATE_WAITING_PLAY,
            ],
            'from waiting_play to won' => [
                'from' => Player::STATE_WAITING_PLAY,
                'to' => Player::TRANSITION_WON,
                'expectedState' => Player::STATE_WON,
            ],
            'from waiting_play to lost' => [
                'from' => Player::STATE_WAITING_PLAY,
                'to' => Player::TRANSITION_LOST,
                'expectedState' => Player::STATE_LOST,
            ],
            'from waiting_opponent_play to won' => [
                'from' => Player::STATE_WAITING_OPPONENT_PLAY,
                'to' => Player::TRANSITION_WON,
                'expectedState' => Player::STATE_WON,
            ],
            'from waiting_opponent_play to lost' => [
                'from' => Player::STATE_WAITING_OPPONENT_PLAY,
                'to' => Player::TRANSITION_LOST,
                'expectedState' => Player::STATE_LOST,
            ],
        ];
    }

    /**
     * testSucessfullyChangePlayerStatus
     * 
     * @dataProvider dataProviderSucessfullyChangePlayerStatus
     *
     * @param string $from
     * @param string $to
     * @param string $expectedState
     * @return void
     */
    public function testSucessfullyChangePlayerStatus(string $from, string $to, string $expectedState): void
    {
        $player = PlayerFixture::getMock([
            'state' => $from,
        ]);

        $this->workflow->changeStatus($to, $player);

        $this->assertSame($player->getState(), $expectedState);
    }

    /**
     * dataProviderFailureChangePlayerStatus
     *
     * @return array
     */
    public function dataProviderFailureChangePlayerStatus(): array
    {
        return [
            'from creating to waiting_play' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_WAITING_PLAY,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_waiting_play" cannot be applied from state "creating"',
            ],
            'from creating to waiting_opponent_play' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_WAITING_OPPONENT_PLAY,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_waiting_opponent_play" cannot be applied from state "creating"',
            ],
            'from creating to won' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_WON,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_won" cannot be applied from state "creating"',
            ],
            'from creating to lost' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_LOST,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_lost" cannot be applied from state "creating"',
            ],
            'from waiting_start to won' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_WON,
                'expectedState' => Player::STATE_WAITING_START,
                'expectedExceptionMessage' => 'Transition "send_to_won" cannot be applied from state "waiting_start"',
            ],
            'from waiting_start to lost' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_LOST,
                'expectedState' => Player::STATE_WAITING_START,
                'expectedExceptionMessage' => 'Transition "send_to_lost" cannot be applied from state "waiting_start"',
            ],
            'from won to lost' => [
                'from' => Player::STATE_WON,
                'to' => Player::TRANSITION_LOST,
                'expectedState' => Player::STATE_WON,
                'expectedExceptionMessage' => 'Transition "send_to_lost" cannot be applied from state "won"',
            ],
            'from lost to won' => [
                'from' => Player::STATE_LOST,
                'to' => Player::TRANSITION_WON,
                'expectedState' => Player::STATE_LOST,
                'expectedExceptionMessage' => 'Transition "send_to_won" cannot be applied from state "lost"',
            ],
        ];
    }

    /**
     * testFailureChangePlayerStatus
     * 
     * @dataProvider dataProviderFailureChangePlayerStatus
     *
     * @param string $from
     * @param string $to
     * @param string $expectedState
     * @param string $expectedExceptionMessage
     * @return void
     */
    public function testFailureChangePlayerStatus(string $from, string $to, string $expectedState, string $expectedExceptionMessage): void
    {
        $this->expectException(WorkflowException::class);
        $this->expectExceptionMessage($expectedExceptionMessage);

        $player = PlayerFixture::getMock([
            'state' => $from,
        ]);

        $this->workflow->changeStatus($to, $player);
    }


    /**
     * dataProviderSucessfullyMultiChangePlayerStatus
     *
     * @return array
     */
    public function dataProviderSucessfullyMultiChangePlayerStatus(): array
    {
        return [
            'multiChange status' => [
                'to' => Player::TRANSITION_WAITING_START,
                'expectedState' => Player::STATE_WAITING_START,
                'players' => [
                    PlayerFixture::getMock([
                        'state' => Player::STATE_CREATING,
                    ]),     
                ],
            ],
        ];
    }

    /**
     * testSucessfullyMultiChangePlayerStatus
     * 
     * @dataProvider dataProviderSucessfullyMultiChangePlayerStatus
     *
     * @param string $to
     * @param string $expectedState
     * @param array  players
     * @return void
     */
    public function testSucessfullyMultiChangePlayerStatus(string $to, string $expectedState, array $players): void
    {
        $this->workflow->multiChangeStatus($to, ...$players);

        foreach ($players as $player) {
            $this->assertSame($player->getState(), $expectedState);
        }
    }
}
