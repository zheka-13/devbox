<?php

namespace App\Services;

use App\Entities\Box\BoxEntity;
use App\Http\Controllers\DTO\SheetDTO;

class DrawService
{
    /**
     * @param BoxEntity[] $boxes
     * @param SheetDTO $sheet
     * @return string
     */
    public function createImage(array $boxes, SheetDTO $sheet): string
    {
        $image = imagecreatetruecolor($sheet->width, $sheet->length);
        $white    = imagecolorallocatealpha($image, 255, 255, 255, 0);
        imagefilledrectangle($image, 0, 0, $sheet->width, $sheet->length, $white);
        foreach ($boxes as $box){
            $color = $this->getColor(rand(1, 100000));
            $box_color = imagecolorallocate($image, $color[0], $color[1], $color[2]);
            $recs = $box->getRectangles();
            foreach ($recs as $rec){
                imagefilledrectangle($image, $rec->getBottomLeft()->getX(), $rec->getBottomLeft()->getY(), $rec->getTopRight()->getX(), $rec->getTopRight()->getY(), $box_color);
            }
        }
        ImagePNG($image, base_path()."/public/test.png");
        ImageDestroy($image);
        return "/test.png";
    }

    private function getColor($num): array
    {
        $hash = md5('color' . $num); // modify 'color' to get a different palette
        return array(
            hexdec(substr($hash, 0, 2)), // r
            hexdec(substr($hash, 2, 2)), // g
            hexdec(substr($hash, 4, 2))); //b
    }
}
