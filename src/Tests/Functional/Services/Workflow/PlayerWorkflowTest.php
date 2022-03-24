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
            'from waiting_play to winner' => [
                'from' => Player::STATE_WAITING_PLAY,
                'to' => Player::TRANSITION_WINNER,
                'expectedState' => Player::STATE_WINNER,
            ],
            'from waiting_play to defeated' => [
                'from' => Player::STATE_WAITING_PLAY,
                'to' => Player::TRANSITION_DEFEATED,
                'expectedState' => Player::STATE_DEFEATED,
            ],
            'from waiting_opponent_play to winner' => [
                'from' => Player::STATE_WAITING_OPPONENT_PLAY,
                'to' => Player::TRANSITION_WINNER,
                'expectedState' => Player::STATE_WINNER,
            ],
            'from waiting_opponent_play to defeated' => [
                'from' => Player::STATE_WAITING_OPPONENT_PLAY,
                'to' => Player::TRANSITION_DEFEATED,
                'expectedState' => Player::STATE_DEFEATED,
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
            'from creating to winner' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_WINNER,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_winner" cannot be applied from state "creating"',
            ],
            'from creating to defeated' => [
                'from' => Player::STATE_CREATING,
                'to' => Player::TRANSITION_DEFEATED,
                'expectedState' => Player::STATE_CREATING,
                'expectedExceptionMessage' => 'Transition "send_to_defeated" cannot be applied from state "creating"',
            ],
            'from waiting_start to winner' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_WINNER,
                'expectedState' => Player::STATE_WAITING_START,
                'expectedExceptionMessage' => 'Transition "send_to_winner" cannot be applied from state "waiting_start"',
            ],
            'from waiting_start to defeated' => [
                'from' => Player::STATE_WAITING_START,
                'to' => Player::TRANSITION_DEFEATED,
                'expectedState' => Player::STATE_WAITING_START,
                'expectedExceptionMessage' => 'Transition "send_to_defeated" cannot be applied from state "waiting_start"',
            ],
            'from winner to defeated' => [
                'from' => Player::STATE_WINNER,
                'to' => Player::TRANSITION_DEFEATED,
                'expectedState' => Player::STATE_WINNER,
                'expectedExceptionMessage' => 'Transition "send_to_defeated" cannot be applied from state "winner"',
            ],
            'from defeated to winner' => [
                'from' => Player::STATE_DEFEATED,
                'to' => Player::TRANSITION_WINNER,
                'expectedState' => Player::STATE_DEFEATED,
                'expectedExceptionMessage' => 'Transition "send_to_winner" cannot be applied from state "defeated"',
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
