<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Area;
use App\Models\Shop;

class ShopController extends Controller
{
    public function index()
    {
        // 1対多 親->子 area_id１に所属する店舗を取得する場合
        $shops = Area::find(1)->shops;
        //dd($shops);
        // 親 <- 子
        $area = Shop::find(3)->area->name;
        //dd($shops, $area);
        // 多対多
        $routes = Shop::find(1)->routes()->get();
        dd($routes);
    }
}
