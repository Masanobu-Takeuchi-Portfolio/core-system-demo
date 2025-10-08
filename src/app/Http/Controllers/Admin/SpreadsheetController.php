<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PhpSpreadsheetService;
use Illuminate\View\View;

class SpreadsheetController extends Controller
{
    protected $spreadsheet;

    /**
     * 
     * @param PhpSpreadsheetService $spreadsheet
     * 
     * @return void
     */
    public function __construct(PhpSpreadsheetService $spreadsheet)
    {
        $this->spreadsheet = $spreadsheet;
    }

    /**
     * Excelダウンロードページを表示.
     *
     * @return View
     */
    public function index(): View
    {
        return view('index');
    }

    /**
     * Excelファイルをダウンロード.
     *
     * @return View
     */
    public function download($id)
    {
        $this->spreadsheet->export($id);
        //return view('index');
    }

    public function downloadFare($id)
    {
        $this->spreadsheet->exportFare($id);
        //return view('index');
    }
}
