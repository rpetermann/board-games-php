<?php

namespace App\Tests\Common;

use App\Dto\SlotDto;
use App\Entity\Game;
use App\Entity\Piece;
use App\Entity\Player;
use App\Services\GameRule\RuleHandler\Checkers\CheckersHandler;
use App\Services\GameRule\RuleHandler\Checkers\PieceRuleHandler\CheckersDefaultPieceHandler;
use App\Tests\Fixture\BoardFixture;
use App\Tests\Fixture\GameFixture;
use App\Tests\Fixture\PieceFixture;
use App\Tests\Fixture\PlayerFixture;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * MockTrait
 */
trait MockTrait
{
    /**
     * createCheckersGameMockWith1Player
     *
     * @return Game
     */
    public function createCheckersGameMockWith1Player(): Game
    {
        $game = GameFixture::getMock([
            'state' => Game::STATE_WAITING_PLAYERS,
        ]);

        $board = BoardFixture::getMock([
            'rows' => CheckersHandler::BOARD_ROWS,
            'columns' => CheckersHandler::BOARD_COLUMNS,
            'type' => CheckersHandler::TYPE,
        ]);
        $game->setBoard($board);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player 1',
            'state' => Player::STATE_WAITING_START,
            'sequence' => 0,
        ]);
        $game->addPlayer($player1);

        return $game;
    }

    /**
     * createCheckersGameMockWith2Players
     *
     * @return Game
     */
    public function createCheckersGameMockWith2Players(): Game
    {
        $game = GameFixture::getMock([
            'state' => Game::STATE_PLAYING,
        ]);

        $board = BoardFixture::getMock([
            'rows' => CheckersHandler::BOARD_ROWS,
            'columns' => CheckersHandler::BOARD_COLUMNS,
            'type' => CheckersHandler::TYPE,
        ]);
        $game->setBoard($board);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player 1',
            'state' => Player::STATE_WAITING_PLAY,
            'sequence' => 0,
        ]);
        $player1Pieces = PieceFixture::getDefaultPlayer1Pieces();
        $player1->setPieces(...$player1Pieces);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player 2',
            'state' => Player::STATE_WAITING_OPPONENT_PLAY,
            'sequence' => 1,
        ]);
        $player2Pieces = PieceFixture::getDefaultPlayer2Pieces();
        $player2->setPieces(...$player2Pieces);

        $game->addPlayer($player1);
        $game->addPlayer($player2);

        return $game;
    }

    /**
     * createCheckersGameMockWith2PlayersWaitingStart
     *
     * @return Game
     */
    public function createCheckersGameMockWith2PlayersWaitingStart(): Game
    {
        $game = GameFixture::getMock([
            'state' => Game::STATE_WAITING_START,
        ]);

        $board = BoardFixture::getMock([
            'rows' => CheckersHandler::BOARD_ROWS,
            'columns' => CheckersHandler::BOARD_COLUMNS,
            'type' => CheckersHandler::TYPE,
        ]);
        $game->setBoard($board);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player 1',
            'state' => Player::STATE_WAITING_START,
            'sequence' => 0,
        ]);
        $player1Pieces = PieceFixture::getDefaultPlayer1Pieces();
        $player1->setPieces(...$player1Pieces);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player 2',
            'state' => Player::STATE_WAITING_START,
            'sequence' => 1,
        ]);
        $player2Pieces = PieceFixture::getDefaultPlayer2Pieces();
        $player2->setPieces(...$player2Pieces);

        $game->addPlayer($player1);
        $game->addPlayer($player2);

        return $game;
    }

    /**
     * createCheckersGameMockWithPiecesToCapture
     *
     * @return Game
     */
    public function createCheckersGameMockWithPiecesToCapture(): Game
    {
        $game = GameFixture::getMock([
            'state' => Game::STATE_PLAYING,
        ]);

        $board = BoardFixture::getMock([
            'rows' => CheckersHandler::BOARD_ROWS,
            'columns' => CheckersHandler::BOARD_COLUMNS,
            'type' => CheckersHandler::TYPE,
        ]);

        $game->setBoard($board);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_PLAY,
            'sequence' => 0,
        ]);
        $player1Pieces = [
            PieceFixture::getMock(['x' => 3, 'y' => 1, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 4, 'y' => 4, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 3, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ];
        $player1->setPieces(...$player1Pieces);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player2',
            'state' => Player::STATE_WAITING_OPPONENT_PLAY,
            'sequence' => 1,
        ]);
        $player2Pieces = [
            PieceFixture::getMock(['x' => 4, 'y' => 2, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 5, 'y' => 3, 'type' => CheckersDefaultPieceHandler::TYPE]),
            PieceFixture::getMock(['x' => 5, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ];
        $player2->setPieces(...$player2Pieces);

        $game->addPlayer($player1);
        $game->addPlayer($player2);

        return $game;
    }

    /**
     * createCheckersGameMockWithLastPieceToFinishGame
     *
     * @return Game
     */
    public function createCheckersGameMockWithLastPieceToFinishGame(): Game
    {
        $game = GameFixture::getMock([
            'state' => Game::STATE_PLAYING,
        ]);

        $board = BoardFixture::getMock([
            'rows' => CheckersHandler::BOARD_ROWS,
            'columns' => CheckersHandler::BOARD_COLUMNS,
            'type' => CheckersHandler::TYPE,
        ]);

        $game->setBoard($board);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_PLAY,
            'sequence' => 0,
        ]);
        $player1Pieces = [
            PieceFixture::getMock(['x' => 4, 'y' => 4, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ];
        $player1->setPieces(...$player1Pieces);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player2',
            'state' => Player::STATE_WAITING_OPPONENT_PLAY,
            'sequence' => 1,
        ]);
        $player2Pieces = [
            PieceFixture::getMock(['x' => 5, 'y' => 5, 'type' => CheckersDefaultPieceHandler::TYPE]),
        ];
        $player2->setPieces(...$player2Pieces);

        $game->addPlayer($player1);
        $game->addPlayer($player2);

        return $game;
    }

    /**
     * getCheckersBoardSlots
     *
     * @return Collection
     */
    public function getCheckersBoardSlots(): Collection
    {
        return new ArrayCollection([
            new SlotDto(0, 0),
            new SlotDto(0, 2),
            new SlotDto(0, 4),
            new SlotDto(0, 6),
            new SlotDto(1, 1),
            new SlotDto(1, 3),
            new SlotDto(1, 5),
            new SlotDto(1, 7),
            new SlotDto(2, 0),
            new SlotDto(2, 2),
            new SlotDto(2, 4),
            new SlotDto(2, 6),
            new SlotDto(3, 1),
            new SlotDto(3, 3),
            new SlotDto(3, 5),
            new SlotDto(3, 7),
            new SlotDto(4, 0),
            new SlotDto(4, 2),
            new SlotDto(4, 4),
            new SlotDto(4, 6),
            new SlotDto(5, 1),
            new SlotDto(5, 3),
            new SlotDto(5, 5),
            new SlotDto(5, 7),
            new SlotDto(6, 0),
            new SlotDto(6, 2),
            new SlotDto(6, 4),
            new SlotDto(6, 6),
            new SlotDto(7, 1),
            new SlotDto(7, 3),
            new SlotDto(7, 5),
            new SlotDto(7, 7),
        ]);
    }
}
