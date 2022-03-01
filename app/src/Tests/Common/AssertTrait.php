<?php

namespace App\Tests\Common;

use App\Entity\History;
use App\Entity\Piece;

trait AssertTrait
{
    /**
     * assertHistoryExists
     *
     * @param History ...$histories
     * @return void
     */
    public function assertHistoryExists(History ...$histories): void
    {
        foreach($histories as $history) {
            $this->assertInstanceOf(History::class, $history);
            $snapshot = $history->getSnapshot();
            $this->assertNotEmpty($snapshot['game']);
        }
    }

    /**
     * assertPieceMovedToPosition
     *
     * @param Piece   $piece
     * @param integer $toX
     * @param integer $toY
     * @return void
     */
    public function assertPieceMovedToPosition(Piece $piece, int $toX, int $toY): void
    {
        $this->assertSame($toX, $piece->getX());
        $this->assertSame($toY, $piece->getY());
    }
}
