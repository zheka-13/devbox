<?php

namespace App\Entities\Box;

class Line
{
    /**
     * @var Point
     */
    private $a;

    /**
     * @var Point
     */
    private $b;

    /**
     * @param Point $a
     * @param Point $b
     */
    public function __construct(Point $a, Point $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    /**
     * @return Point
     */
    public function getA(): Point
    {
        return $this->a;
    }

    /**
     * @return Point
     */
    public function getB(): Point
    {
        return $this->b;
    }


}
