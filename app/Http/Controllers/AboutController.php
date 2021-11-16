<?php

namespace App\Http\Controllers;

use App\Http\Controllers\DTO\BoxDTO;
use App\Http\Controllers\DTO\SheetDTO;
use App\Services\BoxService;
use App\Services\DrawService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AboutController extends Controller
{

    public function __construct()
    {
        ini_set('memory_limit', '2048M');
    }

    public function about()
    {
        return view('about');
    }

    /**
     * @param Request $request
     * @param BoxService $boxService
     * @param DrawService $drawService
     * @return View
     * @throws ValidationException
     */
    public function calculate(Request $request, BoxService $boxService, DrawService $drawService): View
    {
        $start = microtime(true);
        $this->validate($request, [
            "sheet_width" => "required|integer|min:10",
            "sheet_length" => "required|integer|min:10",
            "box_width" => "required|integer|min:10",
            "box_depth" => "required|integer|min:10",
            "box_height" => "required|integer|min:10",
        ]);
        $sheet  = new SheetDTO();
        $sheet->width = (int)$request->input('sheet_width');
        $sheet->length = (int)$request->input('sheet_length');
        $box = new BoxDTO();
        $box->width = (int)$request->input('box_width');
        $box->depth = (int)$request->input('box_depth');
        $box->height = (int)$request->input('box_height');
        $boxes = $boxService->calculateBoxes($sheet, $box);
        $drawService->createImage($boxes, $sheet);
        $pic = $drawService->createImage($boxes, $sheet);
        $width = $sheet->width;
        if ($sheet->width > 400){
            $width = 400;
        }
        $metric = microtime(true) - $start;
        return view('about', ['metric' => round($metric, 3), 'params' => $request->all(), "pic" => $pic, "width" => $width, "count" => count($boxes)]);
    }
}
