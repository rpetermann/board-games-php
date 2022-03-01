<?php

namespace App\Factory;

use App\Entity\Board;
use App\Services\GameRule\GameRule;

/**
 * BoardFactory
 */
class BoardFactory extends AbstractEntityFactory
{
    /**
     * __construct
     *
     * @param GameRule $gameRule
     */
    public function __construct(GameRule $gameRule)
    {
        parent::__construct();
        $this->gameRule = $gameRule;
    }

    /**
     * @param array      $data
     * @param Board|null $board
     *
     * @return Board
     */
    public function make(array $data, ?Board $board = null): Board
    {
        if (is_null($board)) {
            $board = new Board();
        }

        $this->setEntityValue($board, $data);

        return $board;
    }

    /**
     * makeByType
     *
     * @param string $type
     * @return Board
     */
    public function makeByType(string $type): Board
    {
        $handler = $this->gameRule->getHandlerByType($type);

        return $this->make([
            'rows' => $handler->getBoardRows(),
            'columns' => $handler->getBoardColumns(),
            'type' => $handler->getType(),
        ]);
    }
}
