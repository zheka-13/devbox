<?php

namespace App\Entities\Box;

class Rectangle
{
    /**
     * @var Point
     */
    private $bottomLeft;

    /**
     * @var Point
     */
    private $topRight;

    /**
     * @param Point $bottomLeft // bottomLeft coordinates of rectangle
     * @param Point $topRight // topRight coordinates of rectangle
     */
    public function __construct(Point $bottomLeft, Point $topRight)
    {
        $this->bottomLeft = $bottomLeft;
        $this->topRight = $topRight;
    }

    /**
     * @return Point
     */
    public function getBottomLeft(): Point
    {
        return $this->bottomLeft;
    }

    /**
     * @return Point
     */
    public function getTopRight(): Point
    {
        return $this->topRight;
    }

    /**
     * @return Point
     */
    public function getBottomRight(): Point
    {
        return new Point($this->topRight->getX(), $this->bottomLeft->getY());
    }

    /**
     * @return Point
     */
    public function getTopLeft(): Point
    {
        return new Point($this->bottomLeft->getX(), $this->topRight->getY());
    }

    /**
     * @return Line[]
     */
    public function getLines():array
    {
        return [
            new Line($this->getBottomLeft(), $this->getTopLeft()),
            new Line($this->getTopLeft(), $this->getTopRight()),
            new Line($this->getTopRight(), $this->getBottomRight()),
            new Line($this->getBottomRight(), $this->getBottomLeft())
        ];
    }
}
