<x-app-layout>
    <x-slot name="header">
        <div class="flex mdmax:flex-col items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                株式会社清掃　交通費明細
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
                            class="flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center">
                            <div
                                class="bg-slate-300 mdmax:w-[50%] w-[10%] border-r mdmax:border-b border-slate-500">
                                日付</div>
                            <div
                                class="bg-slate-300 mdmax:w-[50%] mdmax:border-b border-slate-500 md:hidden">
                                1日合計金額</div>
                            <div class="bg-slate-300 mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500 md:border-r">
                                交通手段</div>
                            <div
                                class="bg-slate-300 mdmax:w-[50%] mdmax:border-b border-slate-500 md:hidden">
                                金額</div>
                            <div
                                class="bg-slate-300 mdmax:w-[100%]  w-[30%]  mdmax:border-b border-slate-500 md:border-r">
                                交通経路</div>
                            <div
                                class="bg-slate-300 w-[20%] mdmax:border-b border-slate-500 mdmax:hidden md:border-r">
                                金額</div>
                            <div
                                class="bg-slate-300 w-[20%] mdmax:border-b border-slate-500 mdmax:hidden">
                                1日合計金額</div>

                        </div>
                    </div>
                    <form class="mb-8" method="post"
                        action="@isset($fare->id)
                            {{ route('user.fare.update', ['id' => $fare->id]) }}
                        @else
                            {{ route('user.fare.store') }}
                        @endisset">
                        @csrf
                        <input type="hidden" name="year" value="{{ $calendar->year }}">
                        <input type="hidden" name="month" value="{{ $calendar->month }}">

                        {{-- 基本ボックスここから --}}
                        @foreach ($period as $k => $v)
                            <input type="hidden" name="key_date[]" value="{{ $v->format('Y-m-d') }}">
                            {{-- pcレイアウト ここから --}}
                            @if(!$isMobile)
                            <div class="mdmax:hidden">
                                <div
                                    class="flex flex-wrap text-center items-center border border-slate-600">
                                    <div
                                        class="w-[10%] border-r border-slate-500 py-28">
                                        <span class="">{{ $v->format('m月d日') }}</span></div>

                                    <div class="w-[20%] p-1 border-r border-slate-500">
                                        @for ($i = 0; $i < 6; $i++)
                                            <select name="traffic-{{ $v->format('Y-m-d') }}[{{$i}}]" class="w-full" >
                                                <option value="">交通手段を選択</option>
                                                <option value="train" @if('train' === old('traffic-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['traffic'] ?? null)) selected @endif >電車</option>
                                                <option value="bus" @if('bus' === old('traffic-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['traffic'] ?? null)) selected @endif>バス</option>
                                                <option value="parking" @if('parking' === old('traffic-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['traffic'] ?? null)) selected @endif>駐車場</option>
                                            </select>
                                        @endfor
                                    </div>
                                    <div class="w-[30%] p-1 border-r border-slate-500">
                                        @for ($i = 0; $i < 6; $i++)
                                            <div
                                                class="">
                                               <input class="w-full" type="text" name="route-{{ $v->format('Y-m-d') }}[{{$i}}]" value="{{ old('route-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['route'] ?? null)}}" placeholder="経路を入力">
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="w-[20%] p-1 border-r border-slate-500">
                                        @for ($i = 0; $i < 6; $i++)
                                            <div
                                                class="">
                                                <input class="w-full" type="text" name="fare-{{ $v->format('Y-m-d') }}[{{$i}}]" value="{{ old('fare-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['fare'] ?? null)}}" placeholder="金額を入力"> 
                                            </div>
                                        @endfor
                                    </div>
                                    <div class="w-[20%] border-t border-b border-slate-500 bg-fuchsia-300 h-full py-28">
                                        <div class="">
                                        @isset($fare_data[$loop->index]["sub_total"])
                                            {{ number_format($fare_data[$loop->index]["sub_total"]) }}
                                        @endisset
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                            {{-- pcレイアウト ここまで --}}

                            {{-- スマホレイアウトここから --}}
                            @if($isMobile)
                            <div class="mb-4 border border-slate-600 md:hidden">
                                <div
                                    class="flex flex-wrap text-center">
                                    <div
                                        class="mdmax:w-[50%] border-r mdmax:border-b border-slate-500 truncate p-1">
                                        <span>{{ $v->format('m月d日') }}</span>
                                    </div>
                                    <div
                                        class="mdmax:w-[50%] mdmax:border-b border-slate-500 truncate p-1 bg-fuchsia-300">
                                    
                                        <span>
                                            @isset($fare_data[$loop->index]["sub_total"])
                                            {{ number_format($fare_data[$loop->index]["sub_total"]) }}
                                            @endisset
                                        </span>
                                    </div>

                                    {{-- ６段データここから --}}
                                    @for ($i = 0; $i < 6; $i++)
                                    @if($i === 1)
                                    <div class="w-full flex flex-wrap h-28 overflow-auto bg-orange-300">
                                    @endif
                                        <div class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                            <select name="traffic-{{ $v->format('Y-m-d') }}[{{$i}}]" class="w-full">
                                                <option value="">交通手段を選択</option>
                                                <option value="train" @if('train' === old('traffic-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['traffic'] ?? null)) selected @endif >電車</option>
                                                <option value="bus" @if('bus' === old('traffic-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['traffic'] ?? null)) selected @endif>バス</option>
                                            </select>
                                        </div>
                                        <div
                                            class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                            <input class="w-full" type="text" name="fare-{{ $v->format('Y-m-d') }}[{{$i}}]" value="{{ old('fare-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['fare'] ?? null)}}" placeholder="金額を入力"> 
                                        </div>
                                        <div
                                            class="mdmax:w-[1000%] mdmax:border-b border-slate-500 p-1">
                                            <input class="w-full" type="text" name="route-{{ $v->format('Y-m-d') }}[{{$i}}]" value="{{ old('route-' . $v->format('Y-m-d') . '.' . $i, $fare_data[$loop->index]['detail'][$i]['route'] ?? null)}}" placeholder="経路を入力">    
                                        </div>
                                    @endfor
                                    </div>
                                </div>                           
                                
                            </div>
                            @endif
                        @endforeach
                        {{-- 基本ボックスここから --}}
                        {{-- <div class="mb-4 border border-slate-600">
                            <div
                                class="flex flex-wrap text-center">
                                <div
                                    class="mdmax:w-[50%] border-r mdmax:border-b border-slate-500 truncate p-1">
                                   
                                    <span>7月11日</span>
                                </div>
                                <div
                                    class="mdmax:w-[50%] mdmax:border-b border-slate-500 truncate p-1 bg-fuchsia-300">
                                   
                                    <span>440</span>
                                </div>
                                <div class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                    <select name="traffic[]" class="w-full">
                                        <option disabled selected value="">交通手段</option>
                                        <option value="train">電車</option>
                                        <option value="bus">バス</option>
                                    </select>
                                </div>
                                    <div
                                        class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="金額を入力"> 
                                    </div>
                                    <div
                                        class="mdmax:w-[1000%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="経路を入力">    
                                    </div>

                                <div class="w-full flex flex-wrap h-28 overflow-auto bg-orange-300">
                                    <div class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                        <select name="traffic[]" class="w-full">
                                            <option disabled selected value="">交通手段</option>
                                            <option value="train">電車</option>
                                            <option value="bus">バス</option>
                                        </select>
                                    </div>
                                    <div
                                        class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="金額を入力"> 
                                    </div>
                                    <div
                                        class="mdmax:w-[1000%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="経路を入力">    
                                    </div>
                                    
                                    <div class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                        <select name="traffic[]" class="w-full">
                                            <option disabled selected value="">交通手段</option>
                                            <option value="train">電車</option>
                                            <option value="bus">バス</option>
                                        </select>
                                    </div>
                                    <div
                                        class="mdmax:w-[50%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="金額を入力"> 
                                    </div>
                                    <div
                                        class="mdmax:w-[1000%] mdmax:border-b border-slate-500 p-1">
                                        <input id="date" class="w-full" type="text" name="route1[]" value="" placeholder="経路を入力">    
                                    </div>
                                </div>
                                
                            </div>                           
                            
                        </div> --}}
                        {{-- 基本ボックスここまで --}}

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
