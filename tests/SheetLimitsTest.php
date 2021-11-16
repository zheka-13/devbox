<?php

use App\Entities\Box\Exceptions\OutOfLimitsException;
use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;

class SheetLimitsTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testSheetLimit()
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
        $this->expectException(OutOfLimitsException::class);
        $reflection = new \ReflectionClass(BoxService::class);
        $method = $reflection->getMethod('guardLimits');
        $method->setAccessible(true);
        $method->invokeArgs($boxService, [$boxes[0], 400, 400]);
    }


}
