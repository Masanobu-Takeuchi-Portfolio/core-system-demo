<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Models\Job;
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

class JobController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request): View
    {
        //if (isset($request->message)){
        //dd($request->message);
        //}

        //dd($request->session()->all());
        //dd(session('message'));


        // 日付を加工するのでNull 合体演算子 ??は使えない。例：式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
        $now = is_null($request->year) ? new Carbon('now') : new Carbon($request->year . '-' . (intval($request->month) - 1) . '-' . JOB_START_DAY);

        // view側のカレンダー表示のために年と月を取得
        //$calendar['year'] = $now->year;
        $calendar = (object) array();
        //$calendar->year = $now->year;
        $old_month = $now->month;

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
            // 12/11日からの１月分の入力時が来年に対応できていないので年をインクリメント。
            // if ($old_month === 12 && $calendar->month === 1) {
            //     //dd("ここ1", $calendar->year, $calendar->month, $old_month);
            //     $calendar->year = $now->year + 1;
            //     dd("ここ2", $calendar->year, $calendar->month, $old_month);
            // }
        }
        $calendar->year = $now->year;

        // 勤怠に表示する期間を取得
        $period = CarbonPeriod::create($job_start_date, $job_end_date)->toArray();

        // 12/11日からの１月分の入力時が来年に対応できていない
        //dd($calendar->year, $calendar->month);

        // 勤務データがある場合はDBから取得
        $job = Job::where([
            ['user_id', '=', $request->user()->id],
            ['year', '=', $calendar->year],
            ['month', '=', $calendar->month],
        ])->first();

        return view('user.job.index', compact('period', 'calendar', 'job'))
            ->with('JOB_START_YEAR', JOB_START_YEAR)
            ->with('JOB_END_YEAR', JOB_END_YEAR)
            ->with('JOB_START_TIME', JOB_START_TIME)
            ->with('JOB_END_TIME', JOB_END_TIME)
            ->with('job_data', isset($job) ? json_decode($job->data, true) : null)
            //->with('job_data', json_decode($job->data, true) ?? null) // Null 合体演算子。式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
            ->with('job_id', ($job->id) ?? null);
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
        $job_data = array();
        $diff_total = 0.0;
        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $date = date($v);
            $start_timestamp = strtotime($date . " " . $form_data['start_time'][$k]);
            $end_timestamp = strtotime($date . " " . $form_data['end_time'][$k]);
            // var_dump($form_data['rest'][$k]);
            // exit;
            //dd($form_data['rest'][$k]);
            if (Gate::allows('tokyo_staff')) { // 東京スタッフ
                // $worktime = (($end_timestamp - $start_timestamp) / 3600) - floatval($form_data['rest'][$k]);
                // 勤務開始時間もしくは終了時間に空欄があった場合は、差合計を計算させない。
                if ($form_data['start_time'][$k] === EMPTY_JOB || $form_data['end_time'][$k] === EMPTY_JOB || $form_data['rest'][$k] === EMPTY_REST) {
                    $worktime = 0;
                } else {
                    $worktime = (($end_timestamp - $start_timestamp) / 3600) - floatval($form_data['rest'][$k]);
                }
            } else { // 大阪スタッフの場合は休憩時間を勤怠で管理していないため 
                $worktime = (($end_timestamp - $start_timestamp) / 3600);
            }
            //Log::info('worktimeの中確認', ['worktime' => $worktime]);
            //Log::info('start_timeの中確認', ['start_time' => $form_data['start_time'][$k]]);
            $job_data[] = array(
                'date' => $v,
                'start_time' => $form_data['start_time'][$k],
                'start_timestamp' => $start_timestamp,
                'end_time' => $form_data['end_time'][$k],
                'end_timestamp' => $end_timestamp,
                'rest' => Gate::allows('tokyo_staff') ? floatval($form_data['rest'][$k]) : 0,
                'place' => $form_data['place'][$k],
                'worktime' => $worktime,
                'difference' => $worktime !== 0 ? $worktime - DEFAULT_WORK_TIME : 0
            );

            if ($worktime !== 0) {
                $diff_total += $worktime - DEFAULT_WORK_TIME;
            }
        }
        //dd($request->user()->id);
        Job::create([
            'user_id' => $request->user()->id,
            'year' => $request->year,
            'month' => $request->month,
            'diff_total' => $diff_total,
            'data' => json_encode($job_data, JSON_PRETTY_PRINT)
        ]);

        // store処理後はリダイレクトをかける必要あり。
        //return to_route('user.job.index');
        return redirect('/job/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.job_create'));
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
        //dd($id, $request->all());
        $form_data = $request->all();
        $job_data = array();
        $errors = array();
        $diff_total = 0.0;

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $date = date($v);
            $start_timestamp = strtotime($date . " " . $form_data['start_time'][$k]);
            $end_timestamp = strtotime($date . " " . $form_data['end_time'][$k]);
            // var_dump($form_data['rest'][$k]);
            // exit;
            //dd($form_data['rest'][$k]);
            if (Gate::allows('tokyo_staff')) { // 東京スタッフ
                // $worktime = (($end_timestamp - $start_timestamp) / 3600) - floatval($form_data['rest'][$k]);
                // 勤務開始時間もしくは終了時間に空欄があった場合は、差合計を計算させない。
                if ($form_data['start_time'][$k] === EMPTY_JOB || $form_data['end_time'][$k] === EMPTY_JOB || $form_data['rest'][$k] === EMPTY_REST) {
                    // TODO １つでも空欄が入力された場合は、３つの項目が全て空欄でないといけないようにバリデーションを追加する必要あり。
                    // $request->validate([
                    //     'end_time' => ['regex:/^[empty|空欄]+$/u'],
                    // ]);
                    //dd($request);
                    //$request->validator->fails('fdfdfd');
                    // $errors = new ViewErrorBag();
                    // $errors->put('default', $validator->errors());
                    // $request->session()->flash('errors', $errors);
                    $worktime = 0;
                } else {
                    $worktime = (($end_timestamp - $start_timestamp) / 3600) - floatval($form_data['rest'][$k]);
                }
            } else { // 大阪スタッフの場合は休憩時間を勤怠で管理していないため 
                $worktime = (($end_timestamp - $start_timestamp) / 3600);
            }

            $job_data[] = array(
                'date' => $v,
                'start_time' => $form_data['start_time'][$k],
                'start_timestamp' => $start_timestamp,
                'end_time' => $form_data['end_time'][$k],
                'end_timestamp' => $end_timestamp,
                'rest' => Gate::allows('tokyo_staff') ? floatval($form_data['rest'][$k]) : 0,
                'place' => $form_data['place'][$k],
                'worktime' => $worktime,
                'difference' => $worktime !== 0 ? $worktime - DEFAULT_WORK_TIME : 0
            );
            if ($worktime !== 0) {
                $diff_total += $worktime - DEFAULT_WORK_TIME;
            }
        }
        //dd($request->user()->id);
        $Job = Job::find($id);
        $Job->user_id = $request->user()->id;
        $Job->year = $request->year;
        $Job->month = $request->month;
        $Job->data = json_encode($job_data, JSON_PRETTY_PRINT);
        $Job->diff_total = $diff_total;
        $Job->save();

        // Job::create([
        //     'user_id' => $request->user()->id,
        //     'year' => $request->year,
        //     'month' => $request->month,
        //     'data' => json_encode($job_data, JSON_PRETTY_PRINT)
        // ]);

        // store処理後はリダイレクトをかける必要あり。
        //return to_route('user.job.index');
        return redirect('/job/' . $request->year . '/' . $request->month)
            ->with('message', config('constants.user.message.job_update'));
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
