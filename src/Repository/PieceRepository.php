<?php

namespace App\Repository;

use App\Entity\Piece;
use App\Exception\PieceException;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

/**
 * PieceRepository
 */
class PieceRepository extends AbstractRepository
{
    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Piece::class);
    }

    /**
     * getPlayerPieceInGame
     *
     * @param string $pieceId
     * @param string $playerId
     * @param string $gameId
     * @return Piece
     */
    public function getPlayerPieceInGame(string $pieceId, string $playerId, string $gameId): Piece
    {
        $query = $this->createQueryBuilder('pi')
                    ->select()
                    ->innerJoin('App\Entity\Player', 'pl', Join::WITH, 'pi.player = pl.id')
                    ->innerJoin('App\Entity\Game', 'g', Join::WITH, 'pl.game = g.id')
                    ->andWhere('pl.deletedAt IS NULL')
                    ->andWhere('g.deletedAt IS NULL')
                    ->andWhere('pi.id = :pieceId')
                    ->andWhere('pl.id = :playerId')
                    ->andWhere('g.id = :gameId')
                    ->setParameter('pieceId', $pieceId)
                    ->setParameter('playerId', $playerId)
                    ->setParameter('gameId', $gameId)
                    ->getQuery();

        $piece = $query->getOneOrNullResult();

        if (empty($piece)) {
            throw new PieceException(PieceException::TYPE_PIECE_NOT_FOUND);
        }

        return $piece;
    }
}
