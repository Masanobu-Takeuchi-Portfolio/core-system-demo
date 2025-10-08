<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\EditJobRequest;
use Illuminate\View\View;
use App\Models\Job;
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

class JobController extends Controller
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
        $jobs = Job::join('users', 'jobs.user_id', '=', 'users.id')
            ->select('jobs.*', 'users.name_last', 'users.name_first', 'users.email', 'departments.name as department')
            ->join('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.name_last', 'like', '%' . $request->search_name_last ?? '' . '%')
            ->where('users.name_first', 'like', '%' . $request->search_name_first ?? '' . '%')
            ->where('users.email', 'like', '%' . $request->search_email ?? '' . '%')
            ->orderBy('jobs.updated_at', 'desc')
            ->paginate(20);

        // // ページネーション用のパラメータ引き継ぎ
        $append_param = []; // 追加
        $append_param['search_name_last'] = $request->search_name_last;
        $append_param['search_name_first'] = $request->search_name_first;
        $append_param['search_email'] = $request->search_email;
        // 2ページ目以降にパラメーターを引き継ぐ
        $jobs->appends($append_param); // 追加

        return view('admin.job.index', compact('jobs'))
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
    public function show($id) {}

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function search(Request $request): View
    {
        $isMobile = false;
        $fare = array();
        $user = array();
        [$year, $month] = explode('-', $request->history_date);
        //dd($request->all());
        // 日付を加工するのでNull 合体演算子 ??は使えない。例：式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
        $now = is_null($year) ? new Carbon('now') : new Carbon($year . '-' . (intval($month) - 1) . '-' . JOB_START_DAY);

        // view側のカレンダー表示のために年と月を取得
        //$calendar['year'] = $now->year;
        $calendar = (object) array();


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

        $job = Job::where([
            ['user_id', '=', $request->user_id],
            ['year', '=', $calendar->year],
            ['month', '=', $calendar->month],
        ])->first();
        $user = User::find($request->user_id);
        // 過去の履歴日付リストの作成。
        $fares = Job::where([
            ['user_id', '=', $request->user_id]
        ])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $history_date_list = array();
        foreach ($fares as $k => $v) {
            $history_date_list[$v->year][] = $v->month;
        }
        //asort($history_date_list);

        return view('admin.job.edit', compact('period', 'calendar', 'job', 'user'))
            ->with('JOB_START_YEAR', JOB_START_YEAR)
            ->with('JOB_END_YEAR', JOB_END_YEAR)
            ->with('JOB_START_TIME', JOB_START_TIME)
            ->with('JOB_END_TIME', JOB_END_TIME)
            ->with('tokyo_staff', $user->department_id === TOKYO_DEPARTMENT_ID ? true : false)
            ->with('job_data', isset($job) ? json_decode($job->data, true) : null)
            ->with('history_date_list', $history_date_list)
            ->with('history_select', $year . '-' . $month)
            ->with('job_id', ($job->id) ?? null);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request): View
    {
        // 日付を加工するのでNull 合体演算子 ??は使えない。例：式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
        $now = is_null($request->year) ? new Carbon('now') : new Carbon($request->year . '-' . (intval($request->month) - 1) . '-' . JOB_START_DAY);

        // view側のカレンダー表示のために年と月を取得
        //$calendar['year'] = $now->year;
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

        // 勤務データがある場合はDBから取得
        // $job = Job::where([
        //     ['user_id', '=', $request->id],
        //     ['year', '=', $calendar->year],
        //     ['month', '=', $calendar->month],
        // ])->first();
        // $job = Job::where([
        //     ['id', '=', $request->id]
        // ])->first();
        $job = Job::find($request->id);
        $user = Job::find($request->id)->user;
        // 過去の履歴日付リストの作成。
        $fares = Job::where([
            ['user_id', '=', $user->id]
        ])
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        $history_date_list = array();
        foreach ($fares as $k => $v) {
            $history_date_list[$v->year][] = $v->month;
        }
        //dd($history_date_list);
        //asort($history_date_list);
        // $jobs = $job = Job::where([
        //     ['user_id', '=', $user->id]
        // ])->get();
        // dd($jobs);
        //dd($job, $calendar->year, $calendar->month);

        //dd($request->year . '-' . $request->year);

        return view('admin.job.edit', compact('period', 'calendar', 'job', 'user'))
            ->with('JOB_START_YEAR', JOB_START_YEAR)
            ->with('JOB_END_YEAR', JOB_END_YEAR)
            ->with('JOB_START_TIME', JOB_START_TIME)
            ->with('JOB_END_TIME', JOB_END_TIME)
            ->with('tokyo_staff', $user->department_id === TOKYO_DEPARTMENT_ID ? true : false)
            ->with('job_data', isset($job) ? json_decode($job->data, true) : null)
            ->with('tokyo_other_supply', isset($job) ? json_decode($job->tokyo_other_supply, true) : null)
            ->with('tokyo_other_subsidy', isset($job) ? json_decode($job->tokyo_other_subsidy, true) : null)
            ->with('osaka_other_supply', isset($job) ? json_decode($job->osaka_other_supply, true) : null)
            ->with('osaka_other_subsidy', isset($job) ? json_decode($job->osaka_other_subsidy, true) : null)
            ->with('history_date_list', $history_date_list)
            ->with('history_select', $request->year . '-' . $request->month)
            //->with('job_data', json_decode($job->data, true) ?? null) // Null 合体演算子。式 (expr1) ?? (expr2) は、 expr1 が NULL である場合は expr2 
            ->with('job_id', ($job->id) ?? null);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  EditJobRequest $request, int  $id
     * @return \Illuminate\routing\Redirector
     */
    public function update(EditJobRequest $request, $id)
    {
        //dd($id, $request->all());
        //dd($errors);
        $user = Job::find($id)->user;
        $form_data = $request->all();
        $job_data = array();
        $diff_total = 0.0;
        $tokyo_other_supply = array();
        $tokyo_other_subsidy = array();
        $tokyo_other_supply_total = 0;
        $tokyo_other_subsidy_total = 0;
        $osaka_other_supply_total = 0;
        $osaka_other_subsidy_total = 0;
        //dd($form_data);

        // 当月の入力データを取得
        foreach ($form_data['key_date'] as $k => $v) {
            $date = date($v);
            $start_timestamp = strtotime($date . " " . $form_data['start_time'][$k]);
            $end_timestamp = strtotime($date . " " . $form_data['end_time'][$k]);

            if ($user->department_id === TOKYO_DEPARTMENT_ID) { // 東京スタッフ
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
            $job_data[] = array(
                'date' => $v,
                'start_time' => $form_data['start_time'][$k],
                'start_timestamp' => $start_timestamp,
                'end_time' => $form_data['end_time'][$k],
                'end_timestamp' => $end_timestamp,
                'rest' => $user->department_id === TOKYO_DEPARTMENT_ID ? floatval($form_data['rest'][$k]) : 0,
                'place' => $form_data['place'][$k],
                'worktime' => $worktime,
                'difference' => $worktime !== 0 ? $worktime - DEFAULT_WORK_TIME : 0
            );
            if ($worktime !== 0) {
                $diff_total += $worktime - DEFAULT_WORK_TIME;
            }
        }

        $Job = Job::find($id);
        $Job->data = json_encode($job_data, JSON_PRETTY_PRINT);
        $Job->diff_total = $diff_total;
        // 管理者権限追加項目
        $Job->working_days = $form_data['working_days'];
        $Job->overtime_work = $form_data['overtime_work'];
        $Job->base_salary = $form_data['base_salary'];


        $Job->health_insurance = $form_data['health_insurance'];
        $Job->welfare_pension = $form_data['welfare_pension'];
        $Job->unemployment_insurance = $form_data['unemployment_insurance'];
        $Job->income_tax = $form_data['income_tax'];
        $Job->municipal_tax = $form_data['municipal_tax'];
        $Job->notes = $form_data['notes'];
        // 総支給額
        if ($user->department_id === TOKYO_DEPARTMENT_ID) { // 東京スタッフの場合
            $Job->overtime_charge = $form_data['overtime_charge'];
            $Job->nomination_fee = $form_data['nomination_fee'];
            // その他項目の計算
            foreach ($form_data['tokyo_other'] as $k => $v) {
                //dd($v);
                // 総支給額その他項目の入力値をまとめる
                $tokyo_other_supply[] = array(
                    'tokyo_other_name' => $form_data['tokyo_other_name'][$k],
                    'tokyo_other' => $v ?? ''
                );
                if (isset($v)) {
                    $tokyo_other_supply_total += $v;
                }
                // 総控除額その他項目の入力値をまとめる
                $tokyo_other_subsidy[] = array(
                    'tokyo_other_subsidy_name' => $form_data['tokyo_other_subsidy_name'][$k],
                    'tokyo_other_subsidy' => $form_data['tokyo_other_subsidy'][$k] ?? ''
                );
                if (isset($form_data['tokyo_other_subsidy'][$k])) {
                    $tokyo_other_subsidy_total += $form_data['tokyo_other_subsidy'][$k];
                }
            }
            $Job->tokyo_other_supply = json_encode($tokyo_other_supply, JSON_PRETTY_PRINT);
            $Job->tokyo_other_subsidy = json_encode($tokyo_other_subsidy, JSON_PRETTY_PRINT);
            // 総支給額合計計算
            $Job->all_payment = $form_data['base_salary'] + $form_data['overtime_charge'] +
                $form_data['nomination_fee'] + $tokyo_other_supply_total +  $form_data['traffic_expenses'] ?? 0;
        } else { // 大阪スタッフの場合
            $Job->osaka_deemed_overtime_pay = $form_data['osaka_deemed_overtime_pay'];
            $Job->osaka_special_allowance = $form_data['osaka_special_allowance'];
            $Job->osaka_other_allowances = $form_data['osaka_other_allowances'];
            $Job->osaka_traffic_expenses = $form_data['osaka_traffic_expenses'];
            // その他項目の計算
            foreach ($form_data['osaka_other'] as $k => $v) {
                //dd($v);
                // 総支給額その他項目の入力値をまとめる
                $osaka_other_supply[] = array(
                    'osaka_other_name' => $form_data['osaka_other_name'][$k],
                    'osaka_other' => $v ?? ''
                );
                if (isset($v)) {
                    $osaka_other_supply_total += $v;
                }
                // 総控除額その他項目の入力値をまとめる
                $osaka_other_subsidy[] = array(
                    'osaka_other_subsidy_name' => $form_data['osaka_other_subsidy_name'][$k],
                    'osaka_other_subsidy' => $form_data['osaka_other_subsidy'][$k] ?? ''
                );
                if (isset($form_data['osaka_other_subsidy'][$k])) {
                    $osaka_other_subsidy_total += $form_data['osaka_other_subsidy'][$k];
                }
            }
            $Job->osaka_other_supply = json_encode($osaka_other_supply, JSON_PRETTY_PRINT);
            $Job->osaka_other_subsidy = json_encode($osaka_other_subsidy, JSON_PRETTY_PRINT);
            // 総支給額合計計算
            $Job->all_payment = $form_data['base_salary'] + $form_data['osaka_deemed_overtime_pay'] +
                $form_data['osaka_special_allowance'] + $osaka_other_supply_total + $form_data['osaka_other_allowances'] + $form_data['osaka_traffic_expenses'] ?? 0;
        }

        // 控除額合計計算
        $Job->total_deductions = $form_data['health_insurance'] + $form_data['welfare_pension'] +
            $form_data['unemployment_insurance'] + $form_data['income_tax'] + $form_data['municipal_tax']  +
            $tokyo_other_subsidy_total + $osaka_other_subsidy_total;
        $Job->save();

        // store処理後はリダイレクトをかける必要あり。
        return redirect('/admin/job/' . $id . '/' . $request->year . '/' . $request->month . '/edit')
            ->with('history_select', $request->year . '-' . $request->month)
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
