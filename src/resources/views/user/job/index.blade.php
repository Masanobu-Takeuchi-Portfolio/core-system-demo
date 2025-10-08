<x-app-layout>
    <x-slot name="header">
        <div class="flex mdmax:flex-col items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                株式会社清掃　出勤簿
            </h2>
            <form method="get" action="{{ route('user.job.index.search') }}">
                @csrf
                <div class="flex">
                    <select name="year" id="" class="rounded">
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
                    </select>

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
                @can('tokyo_staff')
                    <div class="flex justify-start w-full">
                        <div class="p-2 text-gray-900 mdmax:w-[40%] w-[30%]">
                            <input type="hidden"  id="calc_diff_total" name="calc_diff_total" value="{{ $job->diff_total ?? 0 }}">
                            <dl class="mdmax:w-full w-[50%] p-1 bg-fuchsia-300 flex justify-between">
                                <dd>差合計</dd>
                                <dt id="diff_total">
                                    @isset($job->diff_total)
                                        @php
                                            $minute = ($job->diff_total * 10) % 10 === 0 ? ':00' : ':30';
                                            //echo floor($job->diff_total) . $minute;
                                            echo intval($job->diff_total) . $minute;
                                        @endphp
                                    @else
                                        {{-- ### --}}
                                        0:00
                                    @endisset
                                </dt>
                            </dl>
                        </div>
                        <div class="p-2 text-gray-900 mdmax:w-[60%] w-[70%]">
                            <dl class="mdmax:w-full w-[50%] p-1 bg-fuchsia-300 flex justify-between">
                                <dd>交通費立替金</dd>
                                <dt>円</dt>
                            </dl>
                        </div>
                    </div>
                @endcan
                <div class="p-2 text-gray-900">
                    <div>
                        <div
                            class="flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center">
                            <div
                                class="bg-slate-300 mdmax:w-1/5 @can('tokyo_staff')w-[10%]@else w-[20%] @endcan mdmax:border-b border-slate-500">
                                日付</div>
                            <div class="bg-slate-300 mdmax:w-1/5 w-[10%]  border-l  mdmax:border-b border-slate-500">
                                曜日</div>
                            <div
                                class="bg-slate-300 mdmax:w-[30%]  @can('tokyo_staff')w-[10%]@else w-[20%]@endcan border-l  mdmax:border-b border-slate-500">
                                出勤</div>
                            <div
                                class="bg-slate-300 mdmax:w-[30%]  @can('tokyo_staff')w-[10%]@else w-[20%]@endcan border-l  mdmax:border-b border-slate-500">
                                退勤</div>
                            @can('tokyo_staff')
                                <div class="bg-slate-300 mdmax:w-[33.33%] w-[10%] md:border-l md:border-slate-500">休憩
                                </div>
                                <div class="bg-slate-300 mdmax:w-[33.33%] w-[10%] border-l border-slate-500">
                                    実労働
                                </div>
                                <div class="bg-slate-300 mdmax:w-[33.33%] w-[10%] border-l border-slate-500">
                                    差
                                </div>
                            @endcan
                            <div class="bg-slate-300 mdmax:w-full w-[30%] md:border-l mdmax:border-t border-slate-600">
                                現場名
                            </div>
                        </div>
                    </div>
                    <form class="mb-8" method="post"
                        action="@isset($job_id)
                        {{ route('user.job.update', ['id' => $job_id]) }}
                    @else
                    {{ route('user.job.store') }}
                    @endisset">
                        @csrf
                        <input type="hidden" name="year" value="{{ $calendar->year }}">
                        <input type="hidden" name="month" value="{{ $calendar->month }}">
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
                                @if ($loop->even) bg-blue-300 @endif
                                 border-slate-600 text-center">
                                    {{-- <input type="hidden" name="date-{{$v->format('Y-m-d')}}"
                                    value="{{$v->format('Y-m-d')}}"> --}}
                                    <input type="hidden" name="key_date[]" value="{{ $v->format('Y-m-d') }}">
                                    <div
                                        class="mdmax:w-1/5 @can('tokyo_staff')w-[10%]@else w-[20%]@endcan mdmax:border-b border-slate-500 py-[13px]">
                                        {{ $v->format('m月d日') }}
                                    </div>
                                    <div class="mdmax:w-1/5 w-[10%] border-l mdmax:border-b border-slate-500 py-[13px]">
                                        {{ $v->isoFormat('ddd') }}</div>
                                    <div
                                        class="mdmax:w-[30%] @can('tokyo_staff')w-[10%]@else w-[20%]@endcan border-l mdmax:border-b border-slate-500 p-1">
                                        <select id="start_time_{{$loop->index}}" name="start_time[]" id="" class="rounded kuran difftime">
                                            <option value="0:00">0:00</option>
                                            @isset($job_data) 
                                                <option value="empty" @if ($job_data[$loop->index]['start_time'] === 'empty')selected @endif>休み</option>
                                            @else
                                                <option value="empty">休み</option>
                                            @endisset
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
                                        class="mdmax:w-[30%] @can('tokyo_staff')w-[10%]@else w-[20%]@endcan border-l mdmax:border-b border-slate-500 p-1">
                                        <select id="end_time_{{$loop->index}}" name="end_time[]" id="" class="rounded kuran difftime">
                                            <option value="0:00">0:00</option>
                                            @isset($job_data) 
                                                <option value="empty" @if ($job_data[$loop->index]['end_time'] === 'empty')selected @endif>休み</option>
                                            @else
                                                <option value="empty">休み</option>
                                            @endisset
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
                                    @can('tokyo_staff')
                                        <div class="mdmax:w-[33.33%] w-[10%] md:border-l md:border-slate-500 p-1">
                                            <select id="rest_time_{{$loop->index}}" name="rest[]" id="" class="rounded kuran difftime">
                                                <option value="0"
                                                    @isset($job_data)
                                                    @if ($job_data[$loop->index]['rest'] === 0)
                                                        selected
                                                    @endif
                                                @endisset>
                                                    0:00</option>
                                                @isset($job_data) 
                                                    <option value="99" @if ($job_data[$loop->index]['rest'] === 99)selected @endif>休み</option>
                                                @else
                                                    <option value="99">休み</option>
                                                @endisset
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
                                                {{ $worktime ?? '0:00' }}
                                            </div>
                                        </div>
                                        <div class="mdmax:w-[33.33%] w-[10%] border-l border-slate-500 md:py-[13px] p-1">
                                            <input type="hidden"  id="calc_difference_{{$loop->index}}" name="calc_difference_{{$loop->index}}" value="{{ $job_data[$loop->index]['difference'] ?? -8 }}">
                                            <div id="difference_{{$loop->index}}">
                                                @isset($job_data)
                                                    @php
                                                        $minute =
                                                            ($job_data[$loop->index]['difference'] * 10) % 10 === 0
                                                                ? ':00'
                                                                : ':30';
                                                        if ($job_data[$loop->index]['difference'] === -0.5) {
                                                            $difference = '-0' . $minute;
                                                        } else {
                                                            $difference = floor($job_data[$loop->index]['difference']) . $minute;
                                                        }
                                                    @endphp
                                                @endisset
                                                {{ $difference ?? '-8:00' }}
                                            </div>
                                        </div>
                                    @endcan
                                    <div class="mdmax:w-full
                                        w-[30%] p-1">
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
                            {{-- 基本ボックスここから --}}
                            {{--
                            <div
                                class="flex flex-wrap justify-center items-center border-b border-slate-600 text-center">
                                <div class="mdmax:w-1/5 w-[10%] mdmax:border-b border-slate-500 py-[13px]">
                                    12月11日
                                </div>
                                <div class="mdmax:w-1/5 w-[10%] border-l mdmax:border-b border-slate-500 py-[13px]">
                                    日</div>
                                <div class="mdmax:w-[30%] w-[10%] border-l mdmax:border-b border-slate-500 p-1">
                                    <select name="" id="" class="rounded">
                                        <option value="">10:00</option>
                                    </select>
                                </div>
                                <div class="mdmax:w-[30%] w-[10%] border-l mdmax:border-b border-slate-500 p-1">
                                    <select name="" id="" class="rounded">
                                        <option value="">18:00</option>
                                    </select>
                                </div>
                                <div class="mdmax:w-[33.33%] w-[10%] md:border-l md:border-slate-500 p-1">
                                    <select name="" id="" class="rounded">
                                        <option value="">18:00</option>
                                    </select>
                                </div>
                                <div class="mdmax:w-[33.33%] w-[10%] border-l border-slate-500 md:py-[13px] p-1">
                                    <div>10:00</div>
                                </div>
                                <div class="mdmax:w-[33.33%] w-[10%] border-l border-slate-500 md:py-[13px] p-1">
                                    <div>2:00</div>
                                </div>
                                <div class="mdmax:w-full w-[30%] p-1">
                                    <input class="w-full" type="text" name="search_name_first"
                                        @if (isset($search_name_first)) value={{ $search_name_first }} @endif
                                        placeholder="現場名を入力して下さい。">
                                </div>
                            </div>
                            --}}
                            {{-- 基本ボックスここまで --}}


                        </div>

                        <div class="mt-2">
                            <button
                                class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">保存</button>
                        </div>

                    </form>
                    {{-- <a href="{{ route('admin.userinfo.create') }}" class="block mb-8">
                        <button
                            class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">新規登録</button>
                    </a> --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
