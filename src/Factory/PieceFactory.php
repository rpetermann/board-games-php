<?php

namespace App\Factory;

use App\Entity\Piece;

/**
 * PieceFactory
 */
class PieceFactory extends AbstractEntityFactory
{
    /**
     * @param array      $data
     * @param Piece|null $piece
     *
     * @return Piece
     */
    public function make(array $data, ?Piece $piece = null): Piece
    {
        if (is_null($piece)) {
            $piece = new Piece();
        }

        $this->setEntityValue($piece, $data);

        return $piece;
    }
}
