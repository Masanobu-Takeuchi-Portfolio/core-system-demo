<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EditJobRequest;
use Illuminate\View\View;
use App\Models\Job;
use App\Models\fare as Fare;
use App\Models\Item;
//use App\Models\expense;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

const JOB_SEPARATER = 10; // 出勤簿の次月開始日
const JOB_START_DAY = 11; // 出勤簿の次月開始日
const JOB_START_YEAR = 2024; // 出勤簿の開始年
const JOB_END_YEAR = 2025; // 出勤簿の終了年
const JOB_START_TIME = 8; // 出勤簿の開始時刻
const JOB_END_TIME = 23; // 出勤簿の開始時刻
const DEFAULT_WORK_TIME = 8; // 標準労働時間
const TOKYO_DEPARTMENT_ID = 2; // 東京の部門コード
const EMPTY_JOB = 'empty'; // 勤怠空欄値
const EMPTY_REST = 99; // 休憩時間空欄値
const FARE_START_YEAR = 2024; // 交通費の開始年
const FARE_END_YEAR = 2025; // 交通費の終了年
const OSAKA_INPUT_DEFAULT = 10; // 大阪の初期入力行数

const ITEM_START_YEAR = 2024; // 交通費の開始年
const ITEM_END_YEAR = 2025; // 交通費の終了年
const ITEM_INPUT_DEFAULT = 10; // 初期入力行数


class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        // 外部結合の場合
        // 「->leftJoin(‘users’,’users.id’,’=’,’listpages.user_id’)」箇所が外部結合
        // jobテーブルとuserテーブルを結合
        $items = Item::join('users', 'items.user_id', '=', 'users.id')
            ->select('items.*', 'users.name_last', 'users.name_first', 'users.email', 'departments.name as department')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.name_last', 'like', '%' . $request->search_name_last ?? '' . '%')
            ->where('users.name_first', 'like', '%' . $request->search_name_first ?? '' . '%')
            ->where('users.email', 'like', '%' . $request->search_email ?? '' . '%')
            ->orderBy('items.updated_at', 'desc')
            ->paginate(20);

        // // ページネーション用のパラメータ引き継ぎ
        $append_param = []; // 追加
        $append_param['search_name_last'] = $request->search_name_last;
        $append_param['search_name_first'] = $request->search_name_first;
        $append_param['search_email'] = $request->search_email;
        // 2ページ目以降にパラメーターを引き継ぐ
        $items->appends($append_param); // 追加

        return view('admin.item.index', compact('items'))
            ->with('search_name_last', $request->search_name_last)
            ->with('search_name_first', $request->search_name_first)
            ->with('search_email', $request->search_email);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request): View
    {
        $isMobile = false;
        [$year, $month] = explode('-', $request->history_date);

        // 日付を加工するのでNull 合体演算子 ??は使えない。例：式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
        $now = is_null($year) ? new Carbon('now') : new Carbon($year . '-' . (intval($month) - 1) . '-' . JOB_START_DAY);

        // view側のカレンダー表示のために年と月を取得
        $calendar = (object) array();
        $calendar->year = $now->year;

        if ($now->day <= JOB_SEPARATER) {
            // 当月
            $job_start_date = $now->startOfMonth()->addDay(10)->format('Y-m-d');
            // 次月を跨いでいるので、前月が当月分出勤簿の月となる。
            $calendar->month = $now->month;
            // 月末のオーバーフローを許容できるので、addMonthNoOverflow()は使わずにaddMonthで次月を計算する。
            $job_end_date = $now->addMonth(1)->startOfMonth()->addDay(9)->format('Y-m-d');
        } else { // 月を跨いだ 11日以降 次月の勤怠となる。
            $job_start_date = $now->startOfMonth()->addDay(10)->format('Y-m-d');
            $calendar->month = $now->addMonth(1)->month;
            // 月末のオーバーフローを許容できるので、addMonthNoOverflow()は使わずにaddMonthで次月を計算する。
            $job_end_date = $now->startOfMonth()->addDay(9)->format('Y-m-d');
        }
        // 勤怠に表示する期間を取得
        $period = CarbonPeriod::create($job_start_date, $job_end_date)->toArray();
        $item = Item::where([
            ['user_id', '=', $request->user_id],
            ['year', '=', $calendar->year],
            ['month', '=', $calendar->month],
        ])->first();
        $user = User::find($request->user_id);

        // 過去の履歴日付リストの作成。
        $items = Item::where([
            ['user_id', '=', $request->user_id]
        ])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $history_date_list = array();
        foreach ($items as $k => $v) {
            $history_date_list[$v->year][] = $v->month;
        }
        //dd($items);

        return view('admin.item.edit', compact('period', 'calendar', 'item', 'user'))
            ->with('item_data', isset($item) ? json_decode($item->data, true) : null)
            ->with('ITEM_START_YEAR', ITEM_START_YEAR)
            ->with('linecum', isset($item->data) ? count(json_decode($item->data, true)) : ITEM_INPUT_DEFAULT)
            // ->with('isMobile', $isMobile)
            ->with('history_date_list', $history_date_list)
            ->with('history_select', $year . '-' . $month)
            ->with('ITEM_END_YEAR', ITEM_END_YEAR);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request): View
    {
        $isMobile = false;

        // 日付を加工するのでNull 合体演算子 ??は使えない。例：式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
        $now = is_null($request->year) ? new Carbon('now') : new Carbon($request->year . '-' . (intval($request->month) - 1) . '-' . JOB_START_DAY);

        // view側のカレンダー表示のために年と月を取得
        $calendar = (object) array();
        $calendar->year = $now->year;

        if ($now->day <= JOB_SEPARATER) {
            // 当月
            $job_start_date = $now->startOfMonth()->addDay(10)->format('Y-m-d');
            // 次月を跨いでいるので、前月が当月分出勤簿の月となる。
            $calendar->month = $now->month;
            // 月末のオーバーフローを許容できるので、addMonthNoOverflow()は使わずにaddMonthで次月を計算する。
            $job_end_date = $now->addMonth(1)->startOfMonth()->addDay(9)->format('Y-m-d');
        } else { // 月を跨いだ 11日以降 次月の勤怠となる。
            $job_start_date = $now->startOfMonth()->addDay(10)->format('Y-m-d');
            $calendar->month = $now->addMonth(1)->month;
            // 月末のオーバーフローを許容できるので、addMonthNoOverflow()は使わずにaddMonthで次月を計算する。
            $job_end_date = $now->startOfMonth()->addDay(9)->format('Y-m-d');
        }
        // 勤怠に表示する期間を取得
        $period = CarbonPeriod::create($job_start_date, $job_end_date)->toArray();
        $item = Item::find($request->id);
        $user = Item::find($request->id)->user;

        // 過去の履歴日付リストの作成。
        $items = Item::where([
            ['user_id', '=', $user->id]
        ])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $history_date_list = array();
        foreach ($items as $k => $v) {
            $history_date_list[$v->year][] = $v->month;
        }
        //dd($items);

        return view('admin.item.edit', compact('period', 'calendar', 'item', 'user'))
            ->with('item_data', isset($item) ? json_decode($item->data, true) : null)
            ->with('ITEM_START_YEAR', ITEM_START_YEAR)
            ->with('linecum', isset($item->data) ? count(json_decode($item->data, true)) : ITEM_INPUT_DEFAULT)
            // ->with('isMobile', $isMobile)
            ->with('history_date_list', $history_date_list)
            ->with('history_select', $request->year . '-' . $request->month)
            ->with('ITEM_END_YEAR', ITEM_END_YEAR);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditJobRequest $request, int  $id
     * @return \Illuminate\routing\Redirector
     */
    public function update(EditJobRequest $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
