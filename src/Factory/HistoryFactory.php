<?php

namespace App\Factory;

use App\Dto\HistoryDto;
use App\Entity\History;

/**
 * HistoryFactory
 */
class HistoryFactory extends AbstractEntityFactory
{
    /**
     * @param HistoryDto   $historyDto
     * @param History|null $history
     *
     * @return History
     */
    public function make(HistoryDto $historyDto, ?History $history = null): History
    {
        if (is_null($history)) {
            $history = new History();
        }

        $history->setSnapshot($historyDto->toArray());

        return $history;
    }
}
