<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;
use App\Services\DrawService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ApiController extends Controller
{

    public function __construct()
    {
        ini_set('memory_limit', '2048M');
    }


    /**
     * @param Request $request
     * @param BoxService $boxService
     * @param DrawService $drawService
     * @return JsonResponse
     * @throws ValidationException
     */
    public function simple_box(Request $request, BoxService $boxService, DrawService $drawService): JsonResponse
    {
        //dd($request->all());
        $this->validate($request, [
            "sheetSize.w" => "required|integer|min:10",
            "sheetSize.l" => "required|integer|min:10",
            "boxSize.w" => "required|integer|min:10",
            "boxSize.d" => "required|integer|min:10",
            "boxSize.h" => "required|integer|min:10",
        ]);
        $sheet  = new SheetDTO();
        $sheet->width = (int)$request->input('sheetSize.w');
        $sheet->length = (int)$request->input('sheetSize.l');
        $box = new BoxDTO();
        $box->width = (int)$request->input('boxSize.w');
        $box->depth = (int)$request->input('boxSize.d');
        $box->height = (int)$request->input('boxSize.h');
        $boxes = $boxService->calculateBoxes($sheet, $box);
        $coords = $boxService->getCutLines($boxes, $sheet);
        return new JsonResponse($coords);
    }
}
