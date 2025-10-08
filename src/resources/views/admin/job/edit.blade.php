<x-app-layout>
    <x-slot name="header">
        <div class="flex mdmax:flex-col items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                株式会社清掃　出勤簿
            </h2>
            <form method="get" action="{{ route('admin.job.search') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id ?? null }}" >
                <div class="flex">
                    {{-- <select name="year" id="" class="rounded">
                        @for ($i = $JOB_START_YEAR; $i <= $JOB_END_YEAR; $i++)
                            <option value="{{ $i }}" {{ $calendar->year == $i ? 'selected' : '' }}>
                                {{ $i }}年</option>
                        @endfor
                    </select>
                    <select name="month" id="" class="rounded mx-1">
                        @for ($i = 1; $i <= 12; $i++)
                            <option value="{{ $i }}" {{ $calendar->month == $i ? 'selected' : '' }}>
                                {{ $i }}月</option>
                        @endfor
                    </select> --}}

                    <select name="history_date" id="" class="rounded mx-1">
                        @foreach ($history_date_list as $k => $v)
                            @foreach ($v as $index => $month)
                                <option value="{{ $k }}-{{ $month }}" @isset($history_select) @if($history_select === $k . '-' .$month) selected @endif @endisset >{{ $k }}年{{ $month }}月</option>
                            @endforeach
                        @endforeach
                    </select>

                    {{-- <select name="history" id="" class="rounded mx-1">
                        @foreach ($history_date_list as $k => $v)
                            <option value="{{ $v->year }}-{{ $v->month }}" >{{ $v->year }}年{{ $v->month }}月</option>
                        @endforeach
                    </select> --}}

                    <div class="ms-1">
                        <button
                            class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">表示</button>
                    </div>
                </div>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($errors->any())
                    <ul class="p-2">
                        <li class="list-none {{ config('constants.common.error_message_color') }}">入力にエラーがあります。</li>
                        @foreach ($errors->all() as $error)
                            <li class="list-none {{ config('constants.common.error_message_color') }}">{{$error}}</li>
                        @endforeach
                    </ul>
                @endif
                @if (session('message'))
                    <div class="p-2 {{ config('constants.common.message_color') }}">
                        {{ session('message') }}
                    </div>
                @endif
                <div class="flex justify-between w-full">
                    <div class="p-2 w-[50%]">
                        <dl class="w-full p-1 flex border-solid border-b border-slate-600">
                            <dd>氏名：</dd>
                            <dt>{{ $user->name_last }}&nbsp;{{ $user->name_first }}</dt>
                        </dl>
                    </div>
                    
                    <div class="p-2 w-[50%]">
                        <dl class="w-full p-1 flex border-solid border-b border-slate-600">
                            <dd>対象期間：</dd>
                            <dt>{{ $period[0]->format('n月d日') }}&nbsp;～&nbsp;{{ end($period)->format('n月d日') }}</dt>
                        </dl>
                    </div>
                </div>
                <form class="mb-8" method="post"  id="jobform" name="jobform"
                    action="{{ route('admin.job.update', ['id' => $job_id, 'year' => $calendar->year, 'month' => $calendar->month]) }}">
                    @csrf
                    <input type="hidden" name="year" value="{{ $calendar->year }}">
                    <input type="hidden" name="month" value="{{ $calendar->month }}">

                    <div class="flex justify-between">
                        {{-- 出勤簿 --}}
                        <div class="p-2 text-gray-900 w-[70%]">
                            <div>
                                <div
                                    class="flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center">
                                    <div
                                        class="bg-slate-300 mdmax:w-1/5 @if($tokyo_staff)w-[12%]@else w-[20%] @endif mdmax:border-b border-slate-500">
                                        日付</div>
                                    <div class="bg-slate-300 mdmax:w-1/5 w-[10%]  border-l  mdmax:border-b border-slate-500">
                                        曜日</div>
                                    <div
                                        class="bg-slate-300 mdmax:w-[30%]  @if($tokyo_staff)w-[14%]@else w-[20%]@endif border-l  mdmax:border-b border-slate-500">
                                        出勤</div>
                                    <div
                                        class="bg-slate-300 mdmax:w-[30%]  @if($tokyo_staff)w-[14%]@else w-[20%]@endif border-l  mdmax:border-b border-slate-500">
                                        退勤</div>
                                    @if($tokyo_staff)
                                        <div class="bg-slate-300 mdmax:w-[33.33%] w-[12%] md:border-l md:border-slate-500">休憩
                                        </div>
                                        <div class="bg-slate-300 mdmax:w-[33.33%] w-[10%] border-l border-slate-500">
                                            実労働
                                        </div>
                                        <div class="bg-slate-300 mdmax:w-[33.33%] w-[10%] border-l border-slate-500">
                                            差
                                        </div>
                                    @endif
                                    <div class="bg-slate-300 mdmax:w-full w-[18%] md:border-l mdmax:border-t border-slate-600">
                                        現場名
                                    </div>
                                </div>
                            </div>

                            <div class="border-collapse border border-slate-600">
                                @php
                                    $loop_count = 0;
                                @endphp
                                @foreach ($period as $k => $v)
                                    {{-- $v->format('Y年m月') --}}
                                    {{-- 基本ボックスここから --}}
                                    <div
                                        class="flex flex-wrap justify-center items-center 
                                    @if (!$loop->last) mdmax:border-b-2 border-b @endif 
                                    @if ($loop->even) bg-slate-50 @endif
                                    border-slate-600 text-center">
                                        {{-- <input type="hidden" name="date-{{$v->format('Y-m-d')}}"
                                        value="{{$v->format('Y-m-d')}}"> --}}
                                        <input type="hidden" name="key_date[]" value="{{ $v->format('Y-m-d') }}">
                                        <div
                                            class="mdmax:w-1/5 @if($tokyo_staff)w-[12%]@else w-[20%]@endif mdmax:border-b border-slate-500 py-[13px]">
                                            {{ $v->format('m月d日') }}
                                        </div>
                                        <div class="mdmax:w-1/5 w-[10%] border-l mdmax:border-b border-slate-500 py-[13px]">
                                            {{ $v->isoFormat('ddd') }}</div>
                                        <div
                                            class="mdmax:w-[30%] @if($tokyo_staff)w-[14%]@else w-[20%]@endif border-l mdmax:border-b border-slate-500 p-1">
                                            <select id="start_time_{{$loop->index}}" name="start_time[]" class="rounded kuran difftime">
                                                <option value="0:00">0:00</option>
                                                <option value="empty" @if ($job_data[$loop->index]['start_time'] === 'empty')selected @endif>休み</option>
                                                @for ($i = $JOB_START_TIME; $i <= $JOB_END_TIME; $i += 0.5)
                                                    @php
                                                        $minute = ($i * 10) % 10 === 0 ? ':00' : ':30';
                                                        $time = floor($i) . $minute;
                                                    @endphp
                                                    <option value="{{ $time }}"
                                                        @isset($job_data) 
                                                            @if ($job_data[$loop->index]['start_time'] === $time) 
                                                                selected 
                                                            @endif
                                                        @endisset>
                                                        {{ $time }}</option>
                                                @endfor

                                            </select>
                                        </div>
                                        <div
                                            class="mdmax:w-[30%] @if($tokyo_staff)w-[14%]@else w-[20%]@endif border-l mdmax:border-b border-slate-500 p-1">
                                            <select id="end_time_{{$loop->index}}" name="end_time[]" class="rounded kuran difftime">
                                                <option value="0:00">0:00</option>
                                                <option value="empty" @if ($job_data[$loop->index]['end_time'] === 'empty')selected @endif>休み</option>
                                                @for ($i = $JOB_START_TIME; $i <= $JOB_END_TIME; $i += 0.5)
                                                    @php
                                                        $minute = ($i * 10) % 10 === 0 ? ':00' : ':30';
                                                        $time = floor($i) . $minute;
                                                    @endphp
                                                    {{-- <option value="{{ $time }}">{{ $time }}</option> --}}
                                                    <option value="{{ $time }}"
                                                        @isset($job_data)
                                                            @if ($job_data[$loop->index]['end_time'] === $time)
                                                                selected
                                                            @endif
                                                        @endisset>
                                                        {{ $time }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                        @if($tokyo_staff)
                                            <div class="mdmax:w-[33.33%] w-[12%] md:border-l md:border-slate-500 p-1">
                                                <select id="rest_time_{{$loop->index}}" name="rest[]" class="rounded kuran difftime">
                                                    <option value="0"
                                                        @isset($job_data)
                                                        @if ($job_data[$loop->index]['rest'] === 0)
                                                            selected
                                                        @endif
                                                    @endisset>
                                                        0:00</option>
                                                    <option value="99" @if ($job_data[$loop->index]['rest'] === 99)selected @endif>休み</option>
                                                    <option value="0.5"
                                                        @isset($job_data)
                                                        @if ($job_data[$loop->index]['rest'] === 0.5)
                                                            selected
                                                        @endif
                                                    @endisset>
                                                        0:30</option>
                                                    <option value="1"
                                                        @isset($job_data)
                                                        @if ($job_data[$loop->index]['rest'] === 1)
                                                            selected
                                                        @endif
                                                    @endisset>
                                                        1:00</option>
                                                    {{-- <option value="0.5">0:30</option>
                                                <option value="1">1:00</option> --}}
                                                </select>
                                            </div>
                                            <div class="mdmax:w-[33.33%] w-[10%] border-l border-slate-500 md:py-[13px] p-1">
                                                <div id="worktime_{{$loop->index}}">
                                                    @isset($job_data)
                                                        @php
                                                            $minute =
                                                                ($job_data[$loop->index]['worktime'] * 10) % 10 === 0
                                                                    ? ':00'
                                                                    : ':30';
                                                            $worktime =
                                                                floor($job_data[$loop->index]['worktime']) . $minute;
                                                        @endphp
                                                    @endisset
                                                    {{ $worktime ?? '' }}
                                                </div>
                                            </div>
                                            <div class="mdmax:w-[33.33%] w-[10%] border-l border-slate-500 md:py-[13px] p-1">
                                                <input type="hidden"  id="calc_difference_{{$loop->index}}" name="calc_difference_{{$loop->index}}" value="{{ $job_data[$loop->index]['difference'] ?? 0 }}">
                                                <div id="difference_{{$loop->index}}">
                                                    @isset($job_data)
                                                        @php
                                                            $minute =
                                                                ($job_data[$loop->index]['difference'] * 10) % 10 === 0
                                                                    ? ':00'
                                                                    : ':30';
                                                            // $difference =
                                                            //     floor($job_data[$loop->index]['difference']) . $minute;
                                                            if ($job_data[$loop->index]['difference'] === -0.5) {
                                                                $difference = '-0' . $minute;
                                                            } else {
                                                                $difference = floor($job_data[$loop->index]['difference']) . $minute;
                                                            }
                                                        @endphp
                                                    @endisset
                                                    {{ $difference ?? '' }}
                                                </div>
                                            </div>
                                        @endif
                                        <div class="mdmax:w-full
                                            w-[18%] p-1">
                                            <input class="w-full" type="text" name="place[]" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset
                                                placeholder="現場名を入力して下さい。">
                                        </div>
                                    </div>
                                    {{-- 基本ボックスここまで --}}
                                    @if (!$loop->last)
                                        @php
                                            $loop_count++;
                                        @endphp
                                    @endif
                                @endforeach
                                <input type="hidden" id="loop_count" name="loop_count" value="{{ $loop_count }}">
                            </div>

                            {{-- <div class="mt-2">
                                <button
                                    class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">保存</button>
                            </div> --}}
                        </div>

                        {{-- サイドバー --}}
                        <div class="p-2 w-[30%] flex flex-col items-end">
                            {{-- サイドバー入力欄1 --}}
                            <div class="w-full mb-6">
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">出勤日数</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="working_days" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('working_days', $job['working_days']) }}"
                                                    placeholder="出勤日数を入力">日
                                    </dd>
                                </dl>

                                <dl class="w-full border-solid border border-slate-600 flex items-center">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">時間外労働</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="overtime_work" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('overtime_work', $job['overtime_work']) }}"
                                                    placeholder="時間外労働を入力">時間
                                    </dd>
                                </dl>
                            </div>

                            {{-- サイドバー入力欄2 --}}
                            <div class="w-full mb-6">
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">基本給</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="base_salary" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('base_salary', $job['base_salary']) }}"
                                                    placeholder="基本給を入力">円
                                    </dd>
                                </dl>
                                @if($tokyo_staff)
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">時間外手当</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="overtime_charge" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                        {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                        value="{{ old('overtime_charge', $job['overtime_charge']) }}"
                                                        placeholder="時間外手当を入力">円
                                        </dd>
                                    </dl>
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">交通費立替金</dt>
                                        <dd class="ps-1 pe-2 w-[65%] bg-fuchsia-300 text-right">
                                            <span>円</span>
                                            <input type="hidden" name="traffic_expenses" value="{{ $job['traffic_expenses'] }}">
                                        </dd>
                                    </dl>
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">ご指名手当</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="nomination_fee" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                        {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                        value="{{ old('nomination_fee', $job['nomination_fee']) }}"
                                                        placeholder="ご指名手当を入力">円
                                        </dd>
                                    </dl>
                                    @for ($i = 0; $i < 5; $i++)
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="px-1 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">
                                            <input class="w-[100%]" type="text" name="tokyo_other_name[]" 
                                                value="{{ old('tokyo_other_name.' . $i, $tokyo_other_supply[$i]['tokyo_other_name'] ?? null) }}">
                                        </dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="tokyo_other[]" 
                                                value="{{ old('tokyo_other.' . $i, $tokyo_other_supply[$i]['tokyo_other'] ?? null) }}">円
                                        </dd>
                                    </dl>
                                    @endfor
                                @else
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">みなし残業代</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="osaka_deemed_overtime_pay"
                                                        value="{{ old('osaka_deemed_overtime_pay', $job['osaka_deemed_overtime_pay']) }}"
                                                        placeholder="みなし残業代を入力">円
                                        </dd>
                                    </dl>
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">通勤手当</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="osaka_traffic_expenses"
                                                        value="{{ old('osaka_traffic_expenses', $job['osaka_traffic_expenses']) }}"
                                                        placeholder="通勤手当を入力">円
                                        </dd>
                                    </dl>
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">特別手当</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="osaka_special_allowance"
                                                        value="{{ old('osaka_special_allowance', $job['osaka_special_allowance']) }}"
                                                        placeholder="特別手当を入力">円
                                        </dd>
                                    </dl>
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">その他手当</dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="osaka_other_allowances"
                                                        value="{{ old('osaka_other_allowances', $job['osaka_other_allowances']) }}"
                                                        placeholder="その他手当を入力">円
                                        </dd>
                                    </dl>
                                    @for ($i = 0; $i < 5; $i++)
                                    <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                        <dt class="px-1 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">
                                            <input class="w-[100%]" type="text" name="osaka_other_name[]" 
                                                value="{{ old('osaka_other_name.' . $i, $osaka_other_supply[$i]['osaka_other_name'] ?? null) }}">
                                        </dt>
                                        <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                            <input class="w-[80%]" type="text" name="osaka_other[]" 
                                                value="{{ old('osaka_other.' . $i, $osaka_other_supply[$i]['osaka_other'] ?? null) }}">円
                                        </dd>
                                    </dl>
                                    @endfor
                                @endif
                                <dl class="w-full border-solid border border-slate-600 flex items-center">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 font-bold">総支給額</dt>
                                    <dd class="ps-1 pe-2 w-[65%] text-right">
                                        <span>@isset($job['all_payment'])¥{{ number_format($job['all_payment']) }}@endisset 円</span>
                                    </dd>
                                </dl>
                            </div>

                            {{-- サイドバー入力欄3 --}}
                            <div class="w-full mb-6">
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">健康保険</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="health_insurance" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('health_insurance', $job['health_insurance']) }}"
                                                    placeholder="健康保険を入力">円
                                    </dd>
                                </dl>
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">厚生年金</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="welfare_pension" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('welfare_pension', $job['welfare_pension']) }}"
                                                    placeholder="厚生年金を入力">円
                                    </dd>
                                </dl>
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">雇用保険</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="unemployment_insurance" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('unemployment_insurance', $job['unemployment_insurance']) }}"
                                                    placeholder="雇用保険を入力">円
                                    </dd>
                                </dl>
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">源泉所得税</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="income_tax" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('income_tax', $job['income_tax']) }}"
                                                    placeholder="源泉所得税を入力">円
                                    </dd>
                                </dl>
                                <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">市民税</dt>
                                    <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                        <input class="w-[80%]" type="text" name="municipal_tax" {{-- @if (isset($place)) value={{ $place }} @endif --}}
                                                    {{-- @isset($job_data)  value="{{ $job_data[$loop->index]['place'] }}" @endisset --}}
                                                    value="{{ old('municipal_tax', $job['municipal_tax']) }}"
                                                    placeholder="市民税を入力">円
                                    </dd>
                                </dl>
                                @if($tokyo_staff)
                                    @for ($i = 0; $i < 5; $i++)
                                        <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                            <dt class="px-1 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">
                                                <input class="w-[100%]" type="text" name="tokyo_other_subsidy_name[]" 
                                                    value="{{ old('tokyo_other_subsidy_name.' . $i, $tokyo_other_subsidy[$i]['tokyo_other_subsidy_name'] ?? null) }}">
                                            </dt>
                                            <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                                <input class="w-[80%]" type="text" name="tokyo_other_subsidy[]" 
                                                    value="{{ old('tokyo_other_subsidy.' . $i, $tokyo_other_subsidy[$i]['tokyo_other_subsidy'] ?? null) }}">円
                                            </dd>
                                        </dl>
                                    @endfor
                                @else
                                    @for ($i = 0; $i < 5; $i++)
                                        <dl class="w-full border-solid border border-slate-600 flex items-center border-b-0">
                                            <dt class="px-1 py-3 h-full w-[35%] border-e border-slate-600 bg-slate-300">
                                                <input class="w-[100%]" type="text" name="osaka_other_subsidy_name[]" 
                                                    value="{{ old('osaka_other_subsidy_name.' . $i, $osaka_other_subsidy[$i]['osaka_other_subsidy_name'] ?? null) }}">
                                            </dt>
                                            <dd class="flex justify-between ps-1 items-center pe-2 w-[65%] text-end">
                                                <input class="w-[80%]" type="text" name="osaka_other_subsidy[]" 
                                                    value="{{ old('osaka_other_subsidy.' . $i, $osaka_other_subsidy[$i]['osaka_other_subsidy'] ?? null) }}">円
                                            </dd>
                                        </dl>
                                    @endfor
                                @endif
                                <dl class="w-full border-solid border border-slate-600 flex items-center">
                                    <dt class="ps-2 py-3 h-full w-[35%] border-e border-slate-600 font-bold">控除額合計</dt>
                                    <dd class="ps-1 pe-2 w-[65%] text-right">
                                        <span>@isset($job['total_deductions'])¥{{ number_format($job['total_deductions']) }}@endisset 円</span>
                                    </dd>
                                </dl>
                            </div>

                            {{-- サイドバー備考欄 --}}
                            <div class="w-full border-solid border border-slate-600 mb-6">
                                <div class="p-2">
                                    <div>備考欄</div>
                                    <textarea class="w-full" name="notes" id="">{{ $job['notes'] }}</textarea>
                                </div>
                            </div>
                            @if($tokyo_staff)
                                <div class="w-full">
                                    <input type="hidden"  id="calc_diff_total" name="calc_diff_total" value="{{ $job->diff_total ?? 0 }}">
                                    <dl class="mdmax:w-full w-[50%] p-1 bg-fuchsia-300 flex justify-between">
                                        <dd>差合計</dd>
                                        <dt id="diff_total">
                                            @isset($job->diff_total)
                                                @php
                                                    $minute = ($job->diff_total * 10) % 10 === 0 ? ':00' : ':30';
                                                    echo intval($job->diff_total) . $minute;
                                                @endphp
                                            @else
                                                {{-- ### --}}
                                                0:00
                                            @endisset
                                        </dt>
                                    </dl>
                                </div>
                            @endif

                        </div>
                    </div>
                </form>

                <div class="flex">
                    <div class="p-2">
                        <button id = "job_dowonload" onclick="document.jobform.submit();"
                            class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">保存</button>
                    </div>

                    <div class="p-2">
                        <form action="{{ route('admin.job.download', ['id' => $job_id]) }}" method="POST">
                            @csrf
                        <button
                        class="text-white bg-orange-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">ダウンロード</button>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
