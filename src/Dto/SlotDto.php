<?php

namespace App\Dto;

/**
 * SlotDto
 */
class SlotDto
{
    /**
     * @var integer
     */
    protected $x;

    /**
     * @var integer
     */
    protected $y;

    /**
     * __construct
     *
     * @param integer $x
     * @param integer $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * getX
     *
     * @return integer
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * getY
     *
     * @return integer
     */
    public function getY(): int
    {
        return $this->y;
    }
}
