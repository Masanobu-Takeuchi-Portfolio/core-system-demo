<x-app-layout>
    <x-slot name="header">
        <div class="flex mdmax:flex-col items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                物品購入依頼書
            </h2>
            <form method="get" action="{{ route('admin.item.search') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $user->id ?? null }}">
                <div class="flex">
                    <select name="history_date" id="" class="rounded mx-1">
                        @foreach ($history_date_list as $k => $v)
                        @foreach ($v as $index => $month)
                        <option value="{{ $k }}-{{ $month }}" @isset($history_select) @if($history_select===$k . '-' .$month) selected @endif @endisset>{{ $k }}年{{ $month }}月</option>
                        @endforeach
                        @endforeach
                    </select>

                    <div class="ms-1">
                        <button class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">表示</button>
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
                                class="bg-amber-100 mdmax:w-[50%] w-[12%] border-r mdmax:border-t mdmax:border-b mdmax:border-l border-slate-500">
                                依頼日</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[20%] border-r mdmax:border-t mdmax:border-b border-slate-500">
                                購入種別</div>
                            <div class="bg-amber-100 mdmax:w-[50%] w-[10%] mdmax:border-b mdmax:border-r mdmax:border-l border-slate-500 md:border-r">
                                数量</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[20%]  mdmax:border-b mdmax:border-r border-slate-500 md:border-r">
                                納品先</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[10%] mdmax:border-b mdmax:border-r mdmax:border-l border-slate-500 md:border-r">
                                納期</div>
                            <div
                                class="bg-amber-100 mdmax:w-[50%] w-[28%] mdmax:border-b mdmax:border-r border-slate-500">
                                商品名又は品番など</div>
                        </div>
                    </div>
                    <form class="mb-8" method="post"
                        action="@isset($item->id)
                            {{ route('user.item.update', ['id' => $item->id]) }}
                        @else
                            {{ route('user.item.store') }}
                        @endisset">
                        @csrf
                        <input type="hidden" name="year" value="{{ $calendar->year }}">
                        <input type="hidden" name="month" value="{{ $calendar->month }}">

                        {{-- フォーマットここから --}}
                        <div class="form-fields-container border-b border-slate-500">
                            @for ($i = 0; $i < $linecum; $i++)
                            <div
                                class="flex flex-wrap justify-center items-center border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center p-1">
                                <div
                                    class="mdmax:w-[50%] w-[12%] mdmax:border-b border-r border-slate-500">
                                    <input class="datepic" type="text" name="key_date[{{$i}}]" value="{{old('key_date.' . $i , $item_data[$i]['date'] ?? null)}}" placeholder="日付入力">
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500 ">
                                    <select name="type[{{$i}}]" class="w-full" >
                                        <option value="">選択</option>
                                        <option value="amido" @if('amido' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>網戸資材</option>
                                        <option value="towel" @if('towel' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>タオル</option>
                                        <option value="card" @if('card' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>名刺</option>
                                        <option value="detergent" @if('detergent' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>洗剤等</option>
                                        <option value="other" @if('other' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>その他</option>
                                    </select>
                                </div>
                                <div class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 ">
                                    <input class="w-full" type="text" name="num[]" value="{{ old('num.' . $i, $item_data[$i]['num'] ?? null)}}" placeholder="数量を入力">
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500">
                                    <select name="destination[{{$i}}]" class="w-full" >
                                        <option value="">選択</option>
                                        <option value="home" @if('home' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>自宅</option>
                                        <option value="warehouse" @if('warehouse' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>倉庫</option>
                                        <option value="company" @if('company' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>会社</option>
                                    </select>
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                                    
                                    <select name="deadline[{{$i}}]" class="w-full" >
                                        <option value="">選択</option>
                                        <option value="hurry" @if('hurry' === old('destination.' . $i, $item_data[$i]['deadline'] ?? null)) selected @endif>急ぎ</option>
                                        <option value="normal" @if('normal' === old('destination.' . $i, $item_data[$i]['deadline'] ?? null)) selected @endif>通常</option>
                                    </select>
                                </div>
                                <div
                                    class="mdmax:w-[50%] w-[28%] mdmax:border-b border-slate-500">
                                    <input class="w-full" type="text" name="note[]" value="{{ old('note.' . $i, $item_data[$i]['note'] ?? null)}}" placeholder="商品名・品番を入力">
                                </div>
                            </div>
                            @endfor
                        
                        </div>
                        {{-- フォーマットここまで --}}
                        <button id="add-field-btn" type="button" class="bg-amber-100 p-1 hidden">+入力項目を追加する</button>
                       
                        <div class="mt-2 hidden">
                            <button
                                class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">保存</button>
                        </div>

                    </form>

                    {{-- 追加用フォームここから --}}
                    <div
                        class="addForm hidden flex flex-wrap justify-center items-center  border-t-2 mdmax:border-x-2 md:border-l md:border-r border-slate-600 text-center p-1">
                        <div
                            class="mdmax:w-[50%] w-[12%] mdmax:border-b border-r border-slate-500">
                            <input class="datepic" type="text" name="key_date[{{$i}}]" value="{{old('key_date.' . $i , $item_data[$i]['date'] ?? null)}}" placeholder="日付入力">
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500 ">
                            <select name="type[{{$i}}]" class="w-full" >
                                <option value="">選択</option>
                                <option value="amido" @if('amido' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>網戸資材</option>
                                <option value="towel" @if('towel' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>タオル</option>
                                <option value="card" @if('card' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>名刺</option>
                                <option value="detergent" @if('detergent' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>洗剤等</option>
                                <option value="other" @if('other' === old('type.' . $i, $item_data[$i]['type'] ?? null)) selected @endif>その他</option>
                            </select>
                        </div>
                        <div class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500 ">
                            <input class="w-full" type="text" name="num[]" value="{{ old('num.' . $i, $item_data[$i]['num'] ?? null)}}" placeholder="数量を入力">
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[20%] mdmax:border-b border-slate-500">
                            <select name="destination[{{$i}}]" class="w-full" >
                                <option value="">選択</option>
                                <option value="home" @if('home' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>自宅</option>
                                <option value="warehouse" @if('warehouse' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>倉庫</option>
                                <option value="company" @if('company' === old('destination.' . $i, $item_data[$i]['destination'] ?? null)) selected @endif>会社</option>
                            </select>
                        </div>
                        <div
                            class="mdmax:w-[50%] w-[10%] mdmax:border-b border-slate-500">
                            
                            <select name="deadline[{{$i}}]" class="w-full" >
                                <option value="">選択</option>
                                <option value="hurry" @if('hurry' === old('destination.' . $i, $item_data[$i]['deadline'] ?? null)) selected @endif>急ぎ</option>
                                <option value="normal" @if('normal' === old('destination.' . $i, $item_data[$i]['deadline'] ?? null)) selected @endif>通常</option>
                            </select>
                        </div>
                        <div
                            class="mdmax:w-[100%] w-[28%] mdmax:border-b border-slate-500">
                            <input class="w-full" type="text" name="note[]" value="{{ old('note.' . $i, $item_data[$i]['note'] ?? null)}}" placeholder="商品名・品番を入力">
                        </div>
                    </div>

                    {{-- 追加用フォームここまで --}}

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
