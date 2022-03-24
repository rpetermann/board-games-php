<?php

namespace App\Repository;

use App\Entity\Board;
use Doctrine\Persistence\ManagerRegistry;

/**
 * BoardRepository
 */
class BoardRepository extends AbstractRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Board::class);
    }
}
