<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Job;
use App\Models\Item;
use App\Models\USer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

const JOB_SEPARATER = 10; // 出勤簿の次月開始日
const JOB_START_DAY = 11; // 出勤簿の次月開始日
const JOB_START_YEAR = 2024; // 出勤簿の開始年
const JOB_END_YEAR = 2025; // 出勤簿の終了年
const JOB_START_TIME = 8; // 出勤簿の開始時刻
const JOB_END_TIME = 23; // 出勤簿の開始時刻
const DEFAULT_WORK_TIME = 8; // 標準労働時間
const EMPTY_JOB = 'empty'; // 勤怠空欄値
const EMPTY_REST = 99; // 休憩時間空欄値
const ITEM_START_YEAR = 2024; // 交通費の開始年
const ITEM_END_YEAR = 2025; // 交通費の終了年
const ITEM_INPUT_DEFAULT = 10; // 初期入力行数

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
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

        // 物品データがある場合はDBから取得
        $item = Item::where([
            ['user_id', '=', $request->user()->id],
            ['year', '=', $calendar->year],
            ['month', '=', $calendar->month],
        ])->first();
        $user = User::find($request->user()->id);

        return view('user.item.index', compact('period', 'calendar', 'item', 'user'))
            ->with('item_data', isset($item) ? json_decode($item->data, true) : null)
            ->with('ITEM_START_YEAR', ITEM_START_YEAR)
            ->with('linecum', isset($item->data) ? count(json_decode($item->data, true)) : ITEM_INPUT_DEFAULT)
            // ->with('isMobile', $isMobile)
            ->with('ITEM_END_YEAR', ITEM_END_YEAR);

        // // 交通費データがある場合はDBから取得
        // $fare = Fare::where([
        //     ['user_id', '=', $request->user()->id],
        //     ['year', '=', $calendar->year],
        //     ['month', '=', $calendar->month],
        // ])->first();
        // $user = User::find($request->user()->id);

        // // デバイス判定
        // $user_agent =  $request->header('User-Agent');
        // if ((strpos($user_agent, 'iPhone') !== false)
        //     || (strpos($user_agent, 'iPod') !== false)
        //     || (strpos($user_agent, 'Android') !== false)
        // ) {
        //     $isMobile = true;
        // }

        // if (Gate::allows('tokyo_staff')) { // 東京スタッフ
        //     return view('user.fare.index', compact('period', 'calendar', 'fare', 'user'))
        //         ->with('fare_data', isset($fare) ? json_decode($fare->data, true) : null)
        //         ->with('FARE_START_YEAR', FARE_START_YEAR)
        //         ->with('isMobile', $isMobile)
        //         ->with('FARE_END_YEAR', FARE_END_YEAR);
        // } else { // 大阪スタッフ
        //     return view('user.fare.index_osaka', compact('period', 'calendar', 'fare', 'user'))
        //         ->with('fare_data', isset($fare) ? json_decode($fare->data, true) : null)
        //         ->with('FARE_START_YEAR', FARE_START_YEAR)
        //         ->with('isMobile', $isMobile)
        //         ->with('linecum', isset($fare->data) ? count(json_decode($fare->data, true)) : OSAKA_INPUT_DEFAULT)
        //         ->with('FARE_END_YEAR', FARE_END_YEAR);
        // }
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
        $form_data = $request->all();
        $item_data = array();
        //dd($form_data);

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $validate_key = 'num.' . $k;
            // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
            $data = validator()->validate(
                $request->all(),
                [
                    $validate_key => ['nullable', 'numeric'],
                ],
                [
                    $validate_key => $v . 'の数量は数字で入力して下さい。',
                ]
            );
            $item_data[] = array(
                'date' => $v,
                'type' => $form_data['type'][$k],
                'num' => $form_data['num'][$k],
                'destination' => $form_data['destination'][$k],
                'deadline' => $form_data['deadline'][$k],
                'note' => $form_data['note'][$k],
            );
        }
        Item::create([
            'user_id' => $request->user()->id,
            'year' => $request->year,
            'month' => $request->month,
            'data' => json_encode($item_data, JSON_PRETTY_PRINT)
        ]);

        // store処理後はリダイレクトをかける必要あり。
        return redirect('/item/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.item_create'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $form_data = $request->all();
        //dd($form_data);
        $item_data = array();

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $validate_key = 'num.' . $k;
            // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
            $data = validator()->validate(
                $request->all(),
                [
                    $validate_key => ['nullable', 'numeric'],
                ],
                [
                    $validate_key => $v . 'の数量は数字で入力して下さい。',
                ]
            );
            $item_data[] = array(
                'date' => $v,
                'type' => $form_data['type'][$k],
                'num' => $form_data['num'][$k],
                'destination' => $form_data['destination'][$k],
                'deadline' => $form_data['deadline'][$k],
                'note' => $form_data['note'][$k],
            );
        }

        $fare = Item::find($id);
        $fare->data = json_encode($item_data, JSON_PRETTY_PRINT);
        $fare->save();

        return redirect('/item/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.item_update'));
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
