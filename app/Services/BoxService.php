<?php

namespace App\Services;

use App\Entities\Box\BoxEntity;
use App\Entities\Box\Exceptions\AreaOverlapException;
use App\Entities\Box\Exceptions\OutOfLimitsException;
use App\Entities\Box\Line;
use App\Entities\Box\Point;
use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;

class BoxService
{

    /**
     * @param SheetDTO $sheet
     * @param BoxDTO $box_params
     * @return BoxEntity[]
     */
    public function calculateBoxes(SheetDTO $sheet, BoxDTO $box_params): array
    {
        $boxes = [];
        $step  = min([$box_params->width, $box_params->height, $box_params->depth]);
        for($x = 0; $x <= $sheet->width; $x+=$step){
            for($y = 0; $y <= $sheet->length; $y+=$step) {
                $box = new BoxEntity($x, $y, $box_params);
                $fit = false;
                while (!$box->isFullTurned()){
                    try{
                        $this->guardLimits($box, $sheet->width, $sheet->length);
                    }
                    catch (OutOfLimitsException $e){
                        $box->turn();
                        continue;
                    }
                    try{
                        $this->guardOverlap($boxes, $box);
                    }
                    catch (AreaOverlapException $e){
                        $box->turn();
                        continue;
                    }
                    $fit = true;
                    break;
                }
                if ($fit) {
                    $boxes[] = $box;
                }
            }
        }
        return $boxes;
    }

    /**
     * @param BoxEntity[] $boxes
     * @param SheetDTO $sheet
     * @return array
     */
    public function getProgram(array $boxes, SheetDTO $sheet): array
    {
        $lines = $this->getCutLines($boxes, $sheet);
        $program = [
            ["command" => "START"]
        ];
        $current_x = 0;
        $current_y = 0;
        foreach ($lines as $line){
            if ($line->getA()->getX() != $current_x || $line->getA()->getY() != $current_y){
                $program[] = ["command" => "UP"];
                $program[] = ["command" => "GOTO", "x" => $line->getA()->getX(), "y" => $line->getA()->getY()];
            }
            $program[] = ["command" => "DOWN"];
            $program[] = ["command" => "GOTO", "x" => $line->getB()->getX(), "y" => $line->getB()->getY()];
            $current_x = $line->getB()->getX();
            $current_y = $line->getB()->getY();
        }
        $program[] = ["command" => "STOP"];
        return $program;
    }

    /**
     * @param Line $line
     * @param Line[] $lines
     * @return bool
     */
    public static function exists(Line $line, array $lines): bool
    {
        foreach ($lines as $_line){
            if (self::linesEq($_line, $line)){
                return true;
            }
        }
        return false;
    }

    /**
     * @param Point $a
     * @param Point $b
     * @return bool
     */
    public static function eq(Point $a, Point $b): bool
    {
        if ($a->getX() == $b->getX() && $a->getY() == $b->getY()){
            return true;
        }
        return false;
    }

    /**
     * @param Line $a
     * @param Line $b
     * @return bool
     */
    public static function linesEq(Line $a, Line $b): bool
    {
        if (self::eq($a->getA(), $b->getA()) && self::eq($a->getB(), $b->getB())){
            return true;
        }
        if (self::eq($a->getA(), $b->getB()) && self::eq($a->getB(), $b->getA())){
            return true;
        }
        return false;
    }

    /**test
     * @param BoxEntity[] $boxes
     * @param BoxEntity $box
     * @throws AreaOverlapException
     */
    private function guardOverlap(array $boxes, BoxEntity $box)
    {
        foreach (array_reverse($boxes) as $valid_box) {
            $recs = $valid_box->getRectangles();
            foreach ($recs as $valid_rec) {
                $check_recs = $box->getRectangles();
                foreach ($check_recs as $check_rec) {
                    if ($check_rec->getBottomRight()->getX() <= $valid_rec->getBottomLeft()->getX()){
                        continue;
                    }
                    if ($check_rec->getBottomLeft()->getX() >= $valid_rec->getBottomRight()->getX()){
                        continue;
                    }
                    if ($check_rec->getBottomLeft()->getY() >= $valid_rec->getTopLeft()->getY()){
                        continue;
                    }if ($check_rec->getTopLeft()->getY() <= $valid_rec->getBottomLeft()->getY()){
                        continue;
                    }
                    throw new AreaOverlapException();
                }
            }
        }
    }

    /**
     * @throws OutOfLimitsException
     */
    private function guardLimits(BoxEntity $box, int $limit_x, int $limit_y)
    {
        $recs = $box->getRectangles();
        foreach ($recs as $rec) {
            if ($rec->getTopRight()->getX() < 0 || $rec->getTopRight()->getY() < 0) {
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomLeft()->getX() < 0 || $rec->getBottomLeft()->getY() < 0) {
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomRight()->getX() < 0 || $rec->getBottomRight()->getY() < 0) {
                throw new OutOfLimitsException();
            }
            if ($rec->getTopLeft()->getX() < 0 || $rec->getTopLeft()->getY() < 0) {
                throw new OutOfLimitsException();
            }

            if ($rec->getTopRight()->getX() > $limit_x || $rec->getTopRight()->getY() > $limit_y) {
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomLeft()->getX() > $limit_x || $rec->getBottomLeft()->getY() > $limit_y) {
                throw new OutOfLimitsException();
            }
            if ($rec->getBottomRight()->getX() > $limit_x || $rec->getBottomRight()->getY() > $limit_y) {
                throw new OutOfLimitsException();
            }
            if ($rec->getTopLeft()->getX() > $limit_x || $rec->getTopLeft()->getY() > $limit_y) {
                throw new OutOfLimitsException();
            }
        }
    }

    /**
     * @param BoxEntity[] $boxes
     * @param SheetDTO $sheet
     * @return array
     */
    private function getCutLines(array $boxes, SheetDTO $sheet): array
    {
        $total_lines = [];
        foreach ($boxes as $box){
            $lines = $box->getCutLines();
            foreach ($lines as $line){
                if (self::exists($line, $total_lines)){
                    continue;
                }
                if ($line->getA()->getX() == 0 && $line->getB()->getX() == 0){
                    continue;
                }
                if ($line->getA()->getY() == 0 && $line->getB()->getY() == 0){
                    continue;
                }
                if ($line->getA()->getX() == $sheet->width && $line->getB()->getX() == $sheet->width){
                    continue;
                }
                if ($line->getA()->getY() == $sheet->length && $line->getB()->getY() == $sheet->length){
                    continue;
                }
                $total_lines[] = $line;
            }
        }
        return $total_lines;
    }






}
