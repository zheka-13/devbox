<?php

use App\Entities\Box\Exceptions\AreaOverlapException;
use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;

class BoxOverlapTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testOverlap()
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
        $this->expectException(AreaOverlapException::class);
        $reflection = new \ReflectionClass(BoxService::class);
        $method = $reflection->getMethod('guardOverlap');
        $method->setAccessible(true);
        $method->invokeArgs($boxService, [$boxes, $boxes[0]]);
    }


}
