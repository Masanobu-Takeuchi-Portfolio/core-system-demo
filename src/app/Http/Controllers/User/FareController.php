<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Job;
use App\Models\fare as Fare;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

const JOB_SEPARATER = 10; // 交通費の次月開始日
const JOB_START_DAY = 11; // 交通費の次月開始日
const FARE_START_YEAR = 2024; // 交通費の開始年
const FARE_END_YEAR = 2025; // 交通費の終了年
const JOB_START_TIME = 8; // 交通費の開始時刻
const JOB_END_TIME = 23; // 交通費の開始時刻
const DEFAULT_WORK_TIME = 8; // 標準労働時間
const EMPTY_JOB = 'empty'; // 勤怠空欄値
const EMPTY_REST = 99; // 休憩時間空欄値
const TOKYO_DEPARTMENT_ID = 2; // 東京の部門コード
//const OSAKA_INPUT_DEFAULT = 10; // 大阪の初期入力行数
const OSAKA_INPUT_DEFAULT = 31; // 大阪の初期入力行数

class FareController extends Controller
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
        //$calendar->year = $now->year;

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
        $calendar->year = $now->year;
        // 勤怠に表示する期間を取得
        $period = CarbonPeriod::create($job_start_date, $job_end_date)->toArray();

        // 交通費データがある場合はDBから取得
        $fare = Fare::where([
            ['user_id', '=', $request->user()->id],
            ['year', '=', $calendar->year],
            ['month', '=', $calendar->month],
        ])->first();
        $user = User::find($request->user()->id);

        // デバイス判定
        $user_agent =  $request->header('User-Agent');
        if ((strpos($user_agent, 'iPhone') !== false)
            || (strpos($user_agent, 'iPod') !== false)
            || (strpos($user_agent, 'Android') !== false)
        ) {
            $isMobile = true;
        }

        if (Gate::allows('tokyo_staff')) { // 東京スタッフ
            return view('user.fare.index', compact('period', 'calendar', 'fare', 'user'))
                ->with('fare_data', isset($fare) ? json_decode($fare->data, true) : null)
                ->with('FARE_START_YEAR', FARE_START_YEAR)
                ->with('isMobile', $isMobile)
                ->with('FARE_END_YEAR', FARE_END_YEAR);
        } else { // 大阪スタッフ
            $fare_data = isset($fare) ? json_decode($fare->data, true) : null;
            $activecount = 0;
            if (isset($fare_data)) {
                foreach ($fare_data as $k => $v) {
                    if (isset($fare_data[$k]['date'])) {
                        $activecount++;
                    }
                }
            }
            return view('user.fare.index_osaka', compact('period', 'calendar', 'fare', 'user'))
                ->with('fare_data', isset($fare) ? json_decode($fare->data, true) : null)
                //->with('fare_data', $fare_data)
                ->with('FARE_START_YEAR', FARE_START_YEAR)
                ->with('isMobile', $isMobile)
                ->with('input_num', $activecount >= 10 ? $activecount : 10)
                ->with('linecum', isset($fare->data) ? count(json_decode($fare->data, true)) : OSAKA_INPUT_DEFAULT)
                ->with('FARE_END_YEAR', FARE_END_YEAR);
        }
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
        //Log::info('storeの中確認', $request);
        //dd($request->all());
        $form_data = $request->all();
        $fare_data = array();
        $fare_detail = array();
        $first_total = 0;
        $second_total = 0;

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $fare_detail = array();
            $date = date($v);
            $sub_total = 0;

            if (Gate::allows('tokyo_staff')) { // 東京スタッフ
                if (isset($form_data['traffic-' . $v])) {
                    foreach ($form_data['traffic-' . $v] as $traffic_key => $traffic_val) {
                        $validate_key = 'fare-' . $v . '.' . $traffic_key;
                        //dd($validate_key);
                        // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
                        $data = validator()->validate(
                            $request->all(),
                            [
                                $validate_key => ['nullable', 'numeric'],
                            ],
                            [
                                $validate_key => $date . 'の金額' . ($traffic_key + 1) . 'つ目は数字で入力して下さい。',
                            ]
                        );
                        $dt = new Carbon($v);
                        //dd($dt->day);
                        if (1 <= $dt->day && $dt->day <= 10) {
                            $second_total += $form_data['fare-' . $v][$traffic_key];
                        } else {
                            $first_total += $form_data['fare-' . $v][$traffic_key];
                        }
                        $fare_detail[] = array(
                            'traffic' => $traffic_val,
                            'fare' => $form_data['fare-' . $v][$traffic_key],
                            'route' => $form_data['route-' . $v][$traffic_key]
                        );

                        $sub_total += $form_data['fare-' . $v][$traffic_key];
                    }
                }
                $fare_data[] = array(
                    'date' => $v,
                    'detail' => $fare_detail,
                    'sub_total' => $sub_total,
                );
            } else { // 大阪スタッフの場合は休憩時間を勤怠で管理していないため 
                // $worktime = (($end_timestamp - $start_timestamp) / 3600);
            }
            //Log::info('worktimeの中確認', ['worktime' => $worktime]);
            //Log::info('start_timeの中確認', ['start_time' => $form_data['start_time'][$k]]);

            //dd($v, $fare_detail);


            // if ($worktime !== 0) {
            //     $diff_total += $worktime - DEFAULT_WORK_TIME;
            // }
        }
        //dd($request->user()->id);
        Fare::create([
            'user_id' => $request->user()->id,
            'year' => $request->year,
            'month' => $request->month,
            'first_total' => $first_total,
            'second_total' => $second_total,
            'data' => json_encode($fare_data, JSON_PRETTY_PRINT)
        ]);

        // store処理後はリダイレクトをかける必要あり。
        //return to_route('user.job.index');
        return redirect('/fare/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.fare_create'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeOsaka(Request $request)
    {
        $form_data = $request->all();
        $fare_data = array();
        $osaka_total = 0;

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $validate_key = 'fare.' . $k;
            // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
            $data = validator()->validate(
                $request->all(),
                [
                    $validate_key => ['nullable', 'numeric'],
                ],
                [
                    $validate_key => $v . 'の金額は数字で入力して下さい。',
                ]
            );
            // if (empty($v)) {
            //     continue;
            // }
            $osaka_total += $form_data['fare'][$k];
            $fare_data[] = array(
                'date' => $v,
                'traffic' => $form_data['traffic'][$k],
                'fare' => $form_data['fare'][$k],
                'start' => $form_data['start'][$k],
                'end' => $form_data['end'][$k],
                'way' => $form_data['way'][$k],
                'route' => $form_data['route'][$k]
            );
        }
        Fare::create([
            'user_id' => $request->user()->id,
            'year' => $request->year,
            'month' => $request->month,
            'osaka_total' => $osaka_total,
            'data' => json_encode($fare_data, JSON_PRETTY_PRINT)
        ]);

        // store処理後はリダイレクトをかける必要あり。
        //return to_route('user.job.index');
        return redirect('/fare/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.fare_create'));
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
        $job_data = array();
        $diff_total = 0.0;
        $fare_data = array();
        $fare_detail = array();
        $first_total = 0;
        $second_total = 0;

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $fare_detail = array();
            $date = date($v);
            $sub_total = 0;

            if (Gate::allows('tokyo_staff')) { // 東京スタッフ
                if (isset($form_data['traffic-' . $v])) {
                    foreach ($form_data['traffic-' . $v] as $traffic_key => $traffic_val) {
                        $validate_key = 'fare-' . $v . '.' . $traffic_key;
                        // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
                        $data = validator()->validate(
                            $request->all(),
                            [
                                // fare-2024-07-11.0
                                //'fare-2024-07-11.0' => ['nullable', 'numeric'],
                                $validate_key => ['nullable', 'numeric'],
                            ],
                            [
                                $validate_key => $date . 'の金額' . ($traffic_key + 1) . 'つ目は数字で入力して下さい。',
                            ]
                        );
                        $dt = new Carbon($v);
                        //dd($dt->day);
                        if (1 <= $dt->day && $dt->day <= 10) {
                            $second_total += $form_data['fare-' . $v][$traffic_key];
                        } else {
                            $first_total += $form_data['fare-' . $v][$traffic_key];
                        }
                        $fare_detail[] = array(
                            'traffic' => $traffic_val,
                            'fare' => $form_data['fare-' . $v][$traffic_key],
                            'route' => $form_data['route-' . $v][$traffic_key]
                        );

                        $sub_total += $form_data['fare-' . $v][$traffic_key];
                    }
                }
                $fare_data[] = array(
                    'date' => $v,
                    'detail' => $fare_detail,
                    'sub_total' => $sub_total,
                );
            } else { // 大阪スタッフの場合は休憩時間を勤怠で管理していないため 

            }
        }

        $fare = Fare::find($id);
        $fare->data = json_encode($fare_data, JSON_PRETTY_PRINT);
        $fare->first_total = $first_total;
        $fare->second_total = $second_total;
        $fare->save();

        return redirect('/fare/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.fare_update'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateOsaka(Request $request, $id)
    {
        $form_data = $request->all();
        //dd($form_data);
        $fare_data = array();
        $osaka_total = 0;

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $validate_key = 'fare.' . $k;
            // 金額項目のバリデーションを実施。複雑なためコントローラ側で実装。
            $data = validator()->validate(
                $request->all(),
                [
                    $validate_key => ['nullable', 'numeric'],
                ],
                [
                    $validate_key => $v . 'の金額は数字で入力して下さい。',
                ]
            );

            $osaka_total += $form_data['fare'][$k];
            $fare_data[] = array(
                'date' => $v,
                'traffic' => $form_data['traffic'][$k],
                'fare' => $form_data['fare'][$k],
                'start' => $form_data['start'][$k],
                'end' => $form_data['end'][$k],
                'way' => $form_data['way'][$k],
                'route' => $form_data['route'][$k]
            );
        }

        $fare = Fare::find($id);
        $fare->data = json_encode($fare_data, JSON_PRETTY_PRINT);
        $fare->osaka_total = $osaka_total;
        $fare->save();

        return redirect('/fare/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.fare_update'));
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
