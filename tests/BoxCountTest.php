<?php

use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;

class BoxCountTest extends TestCase
{
    public function testOneBox()
    {
        $boxService = new BoxService();
        $sheet = new SheetDTO();
        $box = new BoxDTO();
        $sheet->width = 800;
        $sheet->length = 600;
        $box->width = 200;
        $box->depth = 200;
        $box->height = 200;
        $boxes = $boxService->calculateBoxes($sheet, $box);
        $this->assertCount(1, $boxes);
    }

    public function testTwoBoxes()
    {
        $boxService = new BoxService();
        $sheet = new SheetDTO();
        $box = new BoxDTO();
        $sheet->width = 1000;
        $sheet->length = 800;
        $box->width = 200;
        $box->depth = 200;
        $box->height = 200;
        $boxes = $boxService->calculateBoxes($sheet, $box);
        $this->assertCount(2, $boxes);
    }

    public function testZeroBoxes()
    {
        $boxService = new BoxService();
        $sheet = new SheetDTO();
        $box = new BoxDTO();
        $sheet->width = 400;
        $sheet->length = 400;
        $box->width = 200;
        $box->depth = 200;
        $box->height = 200;
        $boxes = $boxService->calculateBoxes($sheet, $box);
        $this->assertCount(0, $boxes);
    }
}
