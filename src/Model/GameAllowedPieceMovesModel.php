<?php

namespace App\Model;

use App\Dto\SlotDto;
use Doctrine\Common\Collections\Collection;

/**
 * GameAllowedPieceMovesModel
 */
class GameAllowedPieceMovesModel extends AbstractModel
{
    /**
     * process
     *
     * @param string $gameId
     * @param string $playerId
     * @param string $pieceId
     * @return Collection<int, SlotDto>
     */
    public function process(string $gameId, string $playerId, string $pieceId): Collection
    {
        $piece = $this->pieceRepository->getPlayerPieceInGame(
            $pieceId,
            $playerId,
            $gameId,
        );

        return $this->gameRule->getAllowedPieceMoves($piece);
    }
}
