<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PuppeteerService;
use Illuminate\Http\Request;

class ScrapingController extends Controller
{
    protected $puppeteerService;

    /**
     * @param PuppeteerService $puppeteerService
     * @return void
     */
    public function __construct(PuppeteerService $puppeteerService)
    {
        $this->puppeteerService = $puppeteerService;
    }

    /**
     * スクレイピング入力画面を表示
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.scraping.index');
    }

    /**
     * スクレイピングを実行し結果を表示
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function execute(Request $request)
    {
        $request->validate([
            'url' => ['required', 'url', 'max:2048'],
            'selector' => ['nullable', 'string', 'max:255'],
        ]);

        $url = $request->input('url');
        $selector = $request->input('selector') ?: null;

        $result = $this->puppeteerService->scrape($url, $selector);

        return view('admin.scraping.result', compact('result', 'url', 'selector'));
    }
}
