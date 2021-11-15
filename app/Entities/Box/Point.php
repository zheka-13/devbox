<?php

namespace App\Entities\Box;

class Point
{
    /**
     * @var int
     */
    private $x = 0;
    /**
     * @var int
     */
    private $y = 0;

    /**
     * @param int $x
     * @param int $y
     */
    public function __construct(int $x, int $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    /**
     * @return int
     */
    public function getX(): int
    {
        return $this->x;
    }

    /**
     * @return int
     */
    public function getY(): int
    {
        return $this->y;
    }

}
