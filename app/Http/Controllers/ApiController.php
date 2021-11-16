<?php

namespace App\Http\Controllers;

use App\Entities\Box\Exceptions\SheetTooSmallException;
use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ApiController extends Controller
{

    public function __construct()
    {
        ini_set('memory_limit', '2048M');
    }


    /**
     * @param Request $request
     * @param BoxService $boxService
     * @return JsonResponse
     */
    public function simple_box(Request $request, BoxService $boxService): JsonResponse
    {
        try {
            $this->validate($request, [
                "sheetSize.w" => "required|integer|min:1",
                "sheetSize.l" => "required|integer|min:1",
                "boxSize.w" => "required|integer|min:1",
                "boxSize.d" => "required|integer|min:1",
                "boxSize.h" => "required|integer|min:1",
            ]);
        }
        catch (ValidationException $e){
            return new JsonResponse([
                "success" => false,
                "error" => "Invalid input format. Please use only positive integers"
            ]);
        }

        $sheet  = new SheetDTO();
        $sheet->width = (int)$request->input('sheetSize.w');
        $sheet->length = (int)$request->input('sheetSize.l');
        $box = new BoxDTO();
        $box->width = (int)$request->input('boxSize.w');
        $box->depth = (int)$request->input('boxSize.d');
        $box->height = (int)$request->input('boxSize.h');
        try{
            $this->guardSheetSize($sheet, $box);
        }
        catch (SheetTooSmallException $e)
        {
            return new JsonResponse([
                "success" => false,
                "error" => "Invalid sheet size. Too small for producing at least one box"
            ]);
        }

        $boxes = $boxService->calculateBoxes($sheet, $box);
        $lines = $boxService->getCutLines($boxes, $sheet);
        $program = $boxService->getProgram($lines);
        return new JsonResponse([
            "success" => true,
            "amount" => count($boxes),
            "program" => $program
        ]);
    }

    /**
     * @throws SheetTooSmallException
     */
    private function guardSheetSize(SheetDTO $sheet, BoxDTO $box)
    {
        if ($sheet->length >= 2*$box->height+$box->depth && $sheet->width >= 2*$box->height+2*$box->width){
            return;
        }
        if ($sheet->length >= 2*$box->height+2*$box->width && $sheet->width >= 2*$box->height+$box->depth){
            return;
        }
        throw new SheetTooSmallException();
    }


}
