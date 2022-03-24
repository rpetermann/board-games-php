<?php

namespace App\Repository;

use App\Entity\Player;
use Doctrine\Persistence\ManagerRegistry;

/**
 * PlayerRepository
 */
class PlayerRepository extends AbstractRepository
{
    /**
     * __construct
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Player::class);
    }
}
