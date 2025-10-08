<x-app-layout>
    <x-slot name="header">
        <div class="flex mdmax:flex-col items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                移動交通費申請書
            </h2>
            <form method="get" action="{{ route('user.fare.index.search') }}">
                @csrf
                <div class="flex">
                    <select name="year" id="" class="rounded">
                        @for ($i = $FARE_START_YEAR; $i <= $FARE_END_YEAR; $i++)
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
        <div class="mdmax:w-[100%] mdmax:mt-2 w-[20%] border-b-2 border-slate-600">
            申請者：{{$user->name_last}}&nbsp;{{$user->name_first}}
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                @if ($errors->count())
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
                    @isset($fare->id)
                        <div class="w-full p-2">
                            <div class="text-gray-900 border-2 border-slate-600">
                                <dl class="w-full flex">
                                    <dd class="p-2 w-[70%] h-full border-r-2 border-slate-600 text-center">11～31日　計</dd>
                                    <dt class="p-2 bg-fuchsia-300 w-[30%] h-full text-right">¥{{number_format($fare->first_total)}}</dt>
                                </dl>
                            </div>
                            <div class="text-gray-900 border-x-2 border-b-2 border-slate-600">
                                <dl class="w-full flex">
                                    <dd class="p-2 w-[70%] h-full border-r-2 border-slate-600 text-center">1～10日　計</dd>
                                    <dt class="p-2 bg-fuchsia-300 w-[30%] h-full text-right">¥{{number_format($fare->second_total)}}</dt>
                                </dl>
                            </div>
                        </div>
                    @endisset
                @endcan
                <div class="p-2 text-gray-900">
                    <div class="">
                        <div
                            class="flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center p-1">
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[12%] border-r mdmax:border-b border-slate-500">
                                移動日</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[20%] border-r mdmax:border-b border-slate-500">
                                移動手段又は駐車場</div>
                            <div class="bg-amber-100 mdmax:w-[50%] w-[10%] mdmax:border-b mdmax:border-r border-slate-500 md:border-r">
                                乗車駅</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 md:border-r">
                                降車駅</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[10%]  mdmax:border-b mdmax:border-r border-slate-500 md:border-r">
                                片道・往復</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 md:border-r">
                                料金</div>
                            <div
                                class="bg-amber-100 mdmax:w-[100%] w-[28%] mdmax:border-b border-slate-500">
                                目的地・駐車場</div>
                        </div>
                    </div>
                    <form class="mb-8" method="post"
                        action="@isset($fare->id)
                            {{ route('user.fare.update.osaka', ['id' => $fare->id]) }}
                        @else
                            {{ route('user.fare.store.osaka') }}
                        @endisset">
                        @csrf
                        <input type="hidden" name="year" value="{{ $calendar->year }}">
                        <input type="hidden" name="month" value="{{ $calendar->month }}">
                        <input type="hidden" id="input_num" name="input_num" value="{{$input_num}}">

                        {{-- 大阪フォーマットここから --}}
                        <div class="form-fields-container">
                            @for ($i = 0; $i < $linecum; $i++)
                            <div id="input_box_{{$i}}"
                                class=" @if(!isset($fare_data[$i]['date']) && $i >= 10) hidden @endif flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center p-1">
                                <div
                                    class="mdmax:w-[50%] w-[12%] mdmax:border-b border-r border-slate-500">
                                    <input class="datepic" type="text" name="key_date[{{$i}}]" value="{{old('key_date.' . $i , $fare_data[$i]['date'] ?? null)}}" placeholder="日付入力">
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500 ">
                                    <select name="traffic[{{$i}}]" class="w-full" >
                                        <option value="">選択</option>
                                        <option value="train" @if('train' === old('traffic.' . $i, $fare_data[$i]['traffic'] ?? null)) selected @endif>電車</option>
                                        <option value="parking" @if('parking' === old('traffic.' . $i, $fare_data[$i]['traffic'] ?? null)) selected @endif>駐車場</option>
                                        <option value="bus" @if('bus'===old('traffic.' . $i, $fare_data[$i]['traffic'] ?? null)) selected @endif>バス</option>
                                        
                                    </select>
                                </div>
                                <div class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 ">
                                    <input class="w-full" type="text" name="start[]" value="{{ old('start.' . $i, $fare_data[$i]['start'] ?? null)}}" placeholder="乗車駅を入力">
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                                    <input class="w-full" type="text" name="end[]" value="{{ old('end.' . $i, $fare_data[$i]['end'] ?? null)}}" placeholder="降車駅を入力">
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[10%]  mdmax:border-b border-slate-500 ">
                                    <select name="way[]" class="w-full">
                                        <option value="">選択</option>
                                        <option value="one" @if('one' === old('way.' . $i, $fare_data[$i]['way'] ?? null)) selected @endif>片道</option>
                                        <option value="round" @if('round' === old('way.' . $i, $fare_data[$i]['way'] ?? null)) selected @endif>往復</option>
                                       
                                    </select>
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                                    
                                    <input class="w-full" type="text" name="fare[]" value="{{ old('fare.' . $i, $fare_data[$i]['fare'] ?? null)}}" placeholder="料金を入力">
                                </div>
                                <div
                                    class="mdmax:w-[100%] w-[28%] mdmax:border-b border-slate-500">
                                    <input class="w-full" type="text" name="route[]" value="{{ old('route.' . $i, $fare_data[$i]['route'] ?? null)}}" placeholder="目的地・駐車場を入力">
                                </div>
                            </div>
                            @endfor
                            {{-- <button id="add-field-btn" type="button" class="bg-amber-100 p-1">+入力項目を追加する</button> --}}
                        </div>
                        <dl class="flex justify-between border border-slate-500 w-full">
                            <dt class="w-[50%] flex justify-center border-r border-slate-500"><span>合計</span></dt>
                            <dd class="w-[50%] flex justify-end pr-1"><span>¥@isset($fare->osaka_total) {{number_format($fare->osaka_total)}} @endisset</span></dd>
                        </dl>
                        {{-- 大阪フォーマットここまで --}}
                        <button id="add-field-btn" type="button" class="bg-amber-100 p-1">+入力項目を追加する</button>
                       
                        <div class="mt-2">
                            <button
                                class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">保存</button>
                        </div>

                    </form>
                    {{-- <div class="addForm">
                        <input class="datepic" type="text" name="key_date[]" value="" placeholder="日付入力">
                    </div> --}}

                    {{-- 追加用フォームここから --}}
                    {{-- <div 
                        class="addForm hidden flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center p-1">
                        <div
                            class="mdmax:w-[50%] w-[12%] mdmax:border-b border-r border-slate-500">
                            <input class="datepic" type="text" name="key_date[{{$i}}]" value="{{old('key_date.' . $i , $fare_data[$i]['date'] ?? null)}}" placeholder="日付入力">
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500 ">
                            <select name="traffic[{{$i}}]" class="w-full" >
                                <option value="">選択</option>
                                <option value="train" @if('train' === old('traffic.' . $i, $fare_data[$i]['traffic'] ?? null)) selected @endif>電車</option>
                                <option value="parking" @if('parking' === old('traffic.' . $i, $fare_data[$i]['traffic'] ?? null)) selected @endif>駐車場</option>
                                
                            </select>
                        </div>
                        <div class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 ">
                            <input class="w-full" type="text" name="start[]" value="{{ old('start.' . $i, $fare_data[$i]['start'] ?? null)}}" placeholder="乗車駅を入力">
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                            <input class="w-full" type="text" name="end[]" value="{{ old('end.' . $i, $fare_data[$i]['end'] ?? null)}}" placeholder="降車駅を入力">
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[10%]  mdmax:border-b border-slate-500 ">
                            <select name="way[]" class="w-full">
                                <option value="">選択</option>
                                <option value="one" @if('one' === old('way.' . $i, $fare_data[$i]['way'] ?? null)) selected @endif>片道</option>
                                <option value="round" @if('round' === old('way.' . $i, $fare_data[$i]['way'] ?? null)) selected @endif>往復</option>
                                
                            </select>
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                            
                            <input class="w-full" type="text" name="fare[]" value="{{ old('fare.' . $i, $fare_data[$i]['fare'] ?? null)}}" placeholder="料金を入力">
                        </div>
                        <div
                            class="mdmax:w-[100%] w-[28%] mdmax:border-b border-slate-500">
                            <input class="w-full" type="text" name="route[]" value="{{ old('route.' . $i, $fare_data[$i]['route'] ?? null)}}" placeholder="目的地・駐車場を入力">
                        </div>
                    </div> --}}
                    {{-- 追加用フォームここまで --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
