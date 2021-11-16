<?php

namespace App\Entities\Box;

use App\Http\Controllers\DTO\BoxDTO;
use App\Services\BoxService;

class BoxEntity
{
    private const LAYOUT_LEFT_RIGHT = 'left-right';
    private const LAYOUT_RIGHT_LEFT = 'right-left';
    private const LAYOUT_UP_DOWN = 'up-down';
    private const LAYOUT_DOWN_UP = 'down-up';
    /**
     * @var int
     */
    private $x;

    /**
     * @var int
     */
    private $y;

    /**
     * @var BoxDTO
     */
    private $params;
    /**
     * @var string
     */
    private $layout = self::LAYOUT_UP_DOWN;

    /**
     * @var bool
     */
    private $turned = false;

    /**
     * @param int $x
     * @param int $y
     * @param BoxDTO $params
     */
    public function __construct(int $x, int $y, BoxDTO $params)
    {
        $this->params = $params;
        $this->x = $x;
        $this->y = $y;
    }

    public function turn()
    {
        switch ($this->layout) {
            case self::LAYOUT_LEFT_RIGHT:
                $this->turned = true;
                $this->layout = self::LAYOUT_UP_DOWN;
                break;
            case self::LAYOUT_UP_DOWN:
                $this->layout = self::LAYOUT_RIGHT_LEFT;
                break;
            case self::LAYOUT_RIGHT_LEFT:
                $this->layout = self::LAYOUT_DOWN_UP;
                break;
            case self::LAYOUT_DOWN_UP:
                $this->layout = self::LAYOUT_LEFT_RIGHT;
                break;
        }
    }

    public function isFullTurned():bool
    {
        return  $this->turned;
    }

    /**
     * @return Rectangle
     */
    private function getCentralRectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x, $this->y),
                new Point($this->x + $this->params->depth, $this->y + $this->params->width)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x, $this->y),
                new Point($this->x + $this->params->width , $this->y + $this->params->depth)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x, $this->y),
                new Point($this->x + $this->params->depth, $this->y + $this->params->width)
            );
        }
        return new Rectangle(
            new Point($this->x, $this->y),
            new Point($this->x + $this->params->width, $this->y + $this->params->depth)
        );
    }

    /**
     * @return Rectangle
     */
    private function getUpperRectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x, $this->y + $this->params->width),
                new Point($this->x + $this->params->depth, $this->y + $this->params->width + $this->params->height)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x + $this->params->width, $this->y),
                new Point($this->x + $this->params->width + $this->params->height, $this->y + $this->params->depth)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x, $this->y - $this->params->height),
                new Point($this->x + $this->params->depth, $this->y)
            );
        }
        return new Rectangle(
            new Point($this->x - $this->params->height, $this->y),
            new Point($this->x, $this->y + $this->params->depth)
        );
    }

    /**
     * @return Rectangle
     */
    private function getLeftRectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x - $this->params->height, $this->y),
                new Point($this->x, $this->y + $this->params->width)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x, $this->y + $this->params->depth),
                new Point($this->x + $this->params->width, $this->y + $this->params->depth + $this->params->height)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x + $this->params->depth, $this->y),
                new Point($this->x + $this->params->depth + $this->params->height, $this->y + $this->params->width)
            );
        }
        return new Rectangle(
            new Point($this->x, $this->y - $this->params->height),
            new Point($this->x + $this->params->width, $this->y)
        );
    }

    /**
     * @return Rectangle
     */
    private function getRightRectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x + $this->params->depth, $this->y),
                new Point($this->x + $this->params->depth + $this->params->height, $this->y + $this->params->width)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x, $this->y - $this->params->height),
                new Point($this->x + $this->params->width, $this->y)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x - $this->params->height, $this->y),
                new Point($this->x, $this->y + $this->params->width)
            );
        }
        return new Rectangle(
            new Point($this->x, $this->y + $this->params->depth),
            new Point($this->x + $this->params->width, $this->y + $this->params->depth + $this->params->height)
        );
    }

    /**
     * @return Rectangle
     */
    private function getTailRectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x, $this->y - $this->params->height),
                new Point($this->x + $this->params->depth, $this->y)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x - $this->params->height, $this->y),
                new Point($this->x, $this->y + $this->params->depth)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x, $this->y + $this->params->width),
                new Point($this->x + $this->params->depth, $this->y + $this->params->width + $this->params->height)
            );
        }
        return new Rectangle(
            new Point($this->x + $this->params->width, $this->y),
            new Point($this->x + $this->params->width + $this->params->height, $this->y + $this->params->depth)
        );
    }

    /**
     * @return Rectangle
     */
    private function getTail2Rectangle(): Rectangle
    {
        if ($this->layout == self::LAYOUT_UP_DOWN) {
            return new Rectangle(
                new Point($this->x, $this->y - $this->params->height - $this->params->width),
                new Point($this->x + $this->params->depth, $this->y - $this->params->height)
            );
        }
        if ($this->layout == self::LAYOUT_RIGHT_LEFT) {
            return new Rectangle(
                new Point($this->x - $this->params->height - $this->params->width, $this->y),
                new Point($this->x - $this->params->height, $this->y + $this->params->depth)
            );
        }
        if ($this->layout == self::LAYOUT_DOWN_UP) {
            return new Rectangle(
                new Point($this->x, $this->y + $this->params->width + $this->params->height),
                new Point($this->x + $this->params->depth, $this->y + 2*$this->params->width + $this->params->height)
            );
        }
        return new Rectangle(
            new Point($this->x + $this->params->width + $this->params->height, $this->y),
            new Point($this->x + $this->params->width + $this->params->height + $this->params->width, $this->y + $this->params->depth)
        );
    }

    public function getRectangles(): array
    {
        return [
            $this->getCentralRectangle(),
            $this->getUpperRectangle(),
            $this->getLeftRectangle(),
            $this->getRightRectangle(),
            $this->getTailRectangle(),
            $this->getTail2Rectangle(),
        ];
    }

    /**
     * @return Line[] array
     */
    public function getCutLines(): array
    {
        $coordinates = [];
        $skip_lines = $this->getSkipLines();
        $recs = $this->getRectanglesForCut();
        foreach ($recs as $rec) {
            $lines = $rec->getLines();
            foreach ($lines as $line) {
                if (BoxService::exists($line, $skip_lines)) {
                    continue;
                }
                $coordinates[] = $line;
            }
        }
        return $coordinates;
    }

    /**
     * @return Line[]
     */
    private function getSkipLines(): array
    {
        $centerRect = $this->getCentralRectangle();
        $skip_lines = $centerRect->getLines();
        $tail1_lines = $this->getTailRectangle()->getLines();
        $tail2_lines = $this->getTail2Rectangle()->getLines();
        foreach ($tail1_lines as $tail_line) {
            if (BoxService::exists($tail_line, $tail2_lines)) {
                $skip_lines[] = $tail_line;
            }
        }
        return $skip_lines;
    }

    /**
     * @return Rectangle[]
     */
    private function getRectanglesForCut(): array
    {
        return [
            $this->getUpperRectangle(),
            $this->getLeftRectangle(),
            $this->getRightRectangle(),
            $this->getTailRectangle(),
            $this->getTail2Rectangle(),
        ];
    }


}
