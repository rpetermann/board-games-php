<?php

namespace App\Tests\Unit\Services\GameRule\RuleHandler\Checkers;

use App\Entity\Game;
use App\Entity\Piece;
use App\Entity\Player;
use App\Factory\PieceFactory;
use App\Services\GameRule\PieceRule;
use App\Services\GameRule\RuleHandler\Checkers\CheckersHandler;
use App\Tests\Fixture\GameFixture;
use App\Tests\Fixture\PieceFixture;
use App\Tests\Fixture\PlayerFixture;
use App\Tests\Functional\AbstractWebTestCase;
use Doctrine\Common\Collections\Collection;

/**
 * CheckersHandlerTest
 */
class CheckersHandlerTest extends AbstractWebTestCase
{
    /**
     * @var CheckersHandler
     */
    protected $checkersHandler;

    /**
     * @inheritDoc
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->checkersHandler = new CheckersHandler(
            new PieceRule(),
            new PieceFactory()
        );
    }

    /**
     * testGetType
     *
     * @return void
     */
    public function testGetType(): void
    {
        $response = $this->checkersHandler->getType();

        $this->assertSame('checkers', $response);
    }

    /**
     * testGetBoardRows
     *
     * @return void
     */
    public function testGetBoardRows(): void
    {
        $response = $this->checkersHandler->getBoardRows();

        $this->assertSame(8, $response);
    }

    /**
     * testGetBoardColumns
     *
     * @return void
     */
    public function testGetBoardColumns(): void
    {
        $response = $this->checkersHandler->getBoardColumns();

        $this->assertSame(8, $response);
    }

    //isNumberOfPlayersValid

    /**
     * testIsReachedMaximumNumberOfPlayersWithoutPlayersWillReturnFalse
     *
     * @return void
     */
    public function testIsReachedMaximumNumberOfPlayersWithoutPlayersWillReturnFalse(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_PLAYERS,
        ]);

        $response = $this->checkersHandler->isReachedMaximumNumberOfPlayers($game);

        $this->assertFalse($response);
    }

    /**
     * testIsReachedMaximumNumberOfPlayersWith1PlayerWillReturnFalse
     *
     * @return void
     */
    public function testIsReachedMaximumNumberOfPlayersWith1PlayerWillReturnFalse(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_PLAYERS,
        ]);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_START,
        ]);
        
        $game->addPlayer($player1);

        $response = $this->checkersHandler->isReachedMaximumNumberOfPlayers($game);

        $this->assertFalse($response);
    }

    /**
     * testIsReachedMaximumNumberOfPlayersWith2PlayersWillReturnTrue
     *
     * @return void
     */
    public function testIsReachedMaximumNumberOfPlayersWith2PlayersWillReturnTrue(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_START,
        ]);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_START,
        ]);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player2',
            'state' => Player::STATE_WAITING_START,
        ]);
        
        $game->addPlayer($player1);
        $game->addPlayer($player2);

        $response = $this->checkersHandler->isReachedMaximumNumberOfPlayers($game);

        $this->assertTrue($response);
    }

    /**
     * testIsReachedMinimumNumberOfPlayersWithoutPlayersWillReturnFalse
     *
     * @return void
     */
    public function testIsReachedMinimumNumberOfPlayersWithoutPlayersWillReturnFalse(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_PLAYERS,
        ]);

        $response = $this->checkersHandler->isReachedMinimumNumberOfPlayers($game);

        $this->assertFalse($response);
    }

    /**
     * testIsReachedMinimumNumberOfPlayersWith1PlayerWillReturnFalse
     *
     * @return void
     */
    public function testIsReachedMinimumNumberOfPlayersWith1PlayerWillReturnFalse(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_PLAYERS,
        ]);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_START,
        ]);
        
        $game->addPlayer($player1);

        $response = $this->checkersHandler->isReachedMinimumNumberOfPlayers($game);

        $this->assertFalse($response);
    }

    /**
     * testIsReachedMinimumNumberOfPlayersWith2PlayersWillReturnTrue
     *
     * @return void
     */
    public function testIsReachedMinimumNumberOfPlayersWith2PlayersWillReturnTrue(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_START,
        ]);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_START,
        ]);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player2',
            'state' => Player::STATE_WAITING_START,
        ]);
        
        $game->addPlayer($player1);
        $game->addPlayer($player2);

        $response = $this->checkersHandler->isReachedMinimumNumberOfPlayers($game);

        $this->assertTrue($response);
    }

    /**
     * testCreatePieces
     *
     * @return void
     */
    public function testCreatePieces(): void
    {
        $game = GameFixture::getMock([
            'type' => CheckersHandler::TYPE,
            'state' => Game::STATE_WAITING_START,
        ]);

        $player1 = PlayerFixture::getMock([
            'name' => 'Player1',
            'state' => Player::STATE_WAITING_START,
            'sequence' => 0,
        ]);

        $player2 = PlayerFixture::getMock([
            'name' => 'Player2',
            'state' => Player::STATE_WAITING_START,
            'sequence' => 1,
        ]);
        
        $game->addPlayer($player1);
        $game->addPlayer($player2);

        $this->checkersHandler->createPieces($game);

        $this->assertEqualsCanonicalizing(
            $this->getPiecesPosition(...$this->getExpectedPieces()),
            $this->getPiecesPosition(...$game->getPieces())
        );

        foreach ($game->getPieces() as $piece) {
            $this->assertInstanceOf(Piece::class, $piece);
            $this->assertNotNull($piece->getX());
            $this->assertNotNull($piece->getY());
            $this->assertSame('default', $piece->getType());
        }
    }


    /**
     * dataProviderCaptureOpponentPiece
     *
     * @return array
     */
    public function dataProviderCaptureOpponentPiece(): array
    {
        return [
            'There is no opponent piece to capture' => [
                'playerIdx' => 0,
                'pieceIdx' => 0,
                'toX' => 3,
                'toY' => 3,
                'expectedResponse' => false,
            ],
            'capture opponent piece' => [
                'playerIdx' => 1,
                'pieceIdx' => 0,
                'toX' => 3,
                'toY' => 1,
                'expectedResponse' => true,
            ],
        ];
    }

    /**
     * testCaptureOpponentPiece
     * 
     * @dataProvider dataProviderCaptureOpponentPiece
     *
     * @param integer $playerIdx
     * @param integer $pieceIdx
     * @param integer $toX
     * @param integer $toY
     * @param boolean $expectedResponse
     * @return void
     */
    public function testCaptureOpponentPiece(int $playerIdx, int $pieceIdx, int $toX, int $toY, bool $expectedResponse): void
    {
        $game = $this->createCheckersGameMockWithPiecesToCapture();
        $player = $game->getPlayer()[$playerIdx];
        $piece = $player->getPieces()[$pieceIdx];

        $response = $this->checkersHandler->captureOpponentPiece($piece, $toX, $toY);

        $this->assertSame($expectedResponse, $response);
    }

    /**
     * getExpectedPieces
     *
     * @return Collection<i, Piece>
     */
    protected function getExpectedPieces(): Collection
    {
        return PieceFixture::getDefaultCheckersPieces();
    }

    /**
     * getPiecesPosition
     *
     * @param Piece ...$pieces
     * @return array
     */
    protected function getPiecesPosition(Piece ...$pieces): array
    {
        $positions = [];
        foreach ($pieces as $piece) {
            $positions[] = [
                $piece->getX(),
                $piece->getY(),
            ];
        }

        return $positions;
    }
}
