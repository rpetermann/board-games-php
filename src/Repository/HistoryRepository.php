<?php

namespace App\Repository;

use App\Entity\History;
use Doctrine\Persistence\ManagerRegistry;

/**
 * HistoryRepository
 */
class HistoryRepository extends AbstractRepository
{
    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, History::class);
    }
}
