<?php

namespace App\Entities\Box;

use App\Entities\Box\Exceptions\OutOfLimitsException;
use App\Http\Controllers\DTO\BoxDTO;
use App\Services\BoxService;

class BoxEntity
{
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

    private function getCentralRectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x, $this->y),
            new Point($this->x+$this->params->width, $this->y+$this->params->depth)
        );
    }

    private function getUpperRectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x-$this->params->height, $this->y),
            new Point($this->x, $this->y+$this->params->depth)
        );
    }

    private function getLeftRectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x, $this->y-$this->params->height),
            new Point($this->x+$this->params->width, $this->y)
        );
    }

    private function getRightRectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x, $this->y+$this->params->depth),
            new Point($this->x+$this->params->width, $this->y+$this->params->depth+$this->params->height)
        );
    }

    private function getTailRectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x+$this->params->width, $this->y),
            new Point($this->x+$this->params->width+$this->params->height, $this->y+$this->params->depth)
        );
    }

    private function getTail2Rectangle(): Rectangle
    {
        return new Rectangle(
            new Point($this->x+$this->params->width+$this->params->height, $this->y),
            new Point($this->x+$this->params->width+$this->params->height+$this->params->width, $this->y+$this->params->depth)
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
     * @throws OutOfLimitsException
     */
    public function guardLimits(int $limit_x, int $limit_y)
    {
        $recs = $this->getRectangles();
        foreach ($recs as $rec){
            if ($rec->getTopRight()->getX() < 0 || $rec->getTopRight()->getY() < 0){
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomLeft()->getX() < 0 || $rec->getBottomLeft()->getY() < 0){
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomRight()->getX() < 0 || $rec->getBottomRight()->getY() < 0){
                throw new OutOfLimitsException();
            }
            if ($rec->getTopLeft()->getX() < 0 || $rec->getTopLeft()->getY() < 0){
                throw new OutOfLimitsException();
            }

            if ($rec->getTopRight()->getX() > $limit_x || $rec->getTopRight()->getY() > $limit_y){
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomLeft()->getX() > $limit_x || $rec->getBottomLeft()->getY() > $limit_y){
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomRight()->getX() > $limit_x || $rec->getBottomRight()->getY() > $limit_y){
                throw new OutOfLimitsException();
            }
            if ($rec->getTopLeft()->getX() > $limit_x || $rec->getTopLeft()->getY() > $limit_y){
                throw new OutOfLimitsException();
            }
        }
    }

    /**
     * @return Line[] array
     */
    public function getCutLines(): array
    {
        $coordinates = [];
        $skip_lines = $this->getSkipLines();
        $recs = $this->getRectanglesForCut();
        foreach ($recs as $rec){
            $lines = $rec->getLines();
            foreach ($lines as $line){
                if (BoxService::exists($line, $skip_lines)){
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
        foreach ($tail1_lines as $tail_line){
            if (BoxService::exists($tail_line, $tail2_lines)){
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
