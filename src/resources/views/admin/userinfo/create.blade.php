<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            新規作成
        </h2>
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
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">
                        <form method="post" action="{{ route('admin.userinfo.store') }}">
                            @csrf
                            <div class="container px-5 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_last" class="leading-7 text-sm text-gray-600">姓</label>
                                                <input type="text" id="name_last" name="name_last"
                                                    value="{{ old('name_last') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('name_last')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_first"
                                                    class="leading-7 text-sm text-gray-600">名</label>
                                                <input type="text" id="name_first" name="name_first"
                                                    value="{{ old('name_first') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('name_first')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_first"
                                                    class="leading-7 text-sm text-gray-600">社員番号</label>
                                                <input type="text" id="staff_number" name="staff_number"
                                                    value="{{ old('staff_number') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('staff_number')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_first"
                                                    class="leading-7 text-sm text-gray-600">所属</label>
                                                <select name="department_id" class="w-full" >
                                                    <option value="">選択</option>
                                                    <option value="1" @if('1' === old('department_id')) selected @endif>大阪</option>
                                                    <option value="2" @if('2' === old('department_id')) selected @endif>東京</option>
                                                </select>
                                            </div>
                                            <x-input-error :messages="$errors->get('department_id')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="email"
                                                    class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                                <input type="email" id="email" name="email" value="{{ old('email') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="password"
                                                    class="leading-7 text-sm text-gray-600">パスワード</label>
                                                <input type="password" id="password" name="password"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="password"
                                                    class="leading-7 text-sm text-gray-600">パスワード（確認用）</label>
                                                <input type="password" id="password_confirmation"
                                                    name="password_confirmation"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('password_confirmation')"
                                                class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="url_transportation_expenses"
                                                    class="leading-7 text-sm text-gray-600">交通費URL</label>
                                                <input type="url_transportation_expenses"
                                                    id="url_transportation_expenses" name="url_transportation_expenses"
                                                    value="{{ old('url_transportation_expenses') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('url_transportation_expenses')"
                                                class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="url_schedule"
                                                    class="leading-7 text-sm text-gray-600">スケジュールURL</label>
                                                <input type="url_schedule" id="url_schedule" name="url_schedule"
                                                    value="{{ old('url_schedule') }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('url_schedule')" class="mt-2" />
                                        </div>

                                        {{-- <div class="p-2 w-full">
                                            <div class="relative">
                                                <label class="leading-7 text-sm text-gray-600">性別</label><br>
                                                <input type="radio" name="gender" value="0" {{ old('gender')==0
                                                    ? 'checked' : '' }}>男性
                                                <input type="radio" name="gender" value="1" {{ old('gender')==1
                                                    ? 'checked' : '' }}>女性
                                            </div>
                                            <x-input-error :messages="$errors->get('gender')" class="mt-2" />
                                        </div> --}}

                                        {{-- <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="url" class="leading-7 text-sm text-gray-600">年齢</label>
                                                <select name="age">
                                                    <option value="">選択してください</option>
                                                    <option value="1" {{ old('age')==1 ? 'selected' : '' }}>～19歳
                                                    </option>
                                                    <option value="2" {{ old('age')==2 ? 'selected' : '' }}>
                                                        20歳～29歳</option>
                                                    <option value="3" {{ old('age')==3 ? 'selected' : '' }}>
                                                        30歳～39歳</option>
                                                    <option value="4" {{ old('age')==4 ? 'selected' : '' }}>
                                                        40歳～49歳</option>
                                                    <option value="5" {{ old('age')==5 ? 'selected' : '' }}>
                                                        50歳～59歳</option>
                                                    <option value="6" {{ old('age')==6 ? 'selected' : '' }}>
                                                        60歳～</option>
                                                </select>
                                            </div>
                                            <x-input-error :messages="$errors->get('url')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="contact"
                                                    class="leading-7 text-sm text-gray-600">お問い合わせ内容</label>
                                                <textarea id="contact" name="contact"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 h-32 text-base outline-none text-gray-700 py-1 px-3 resize-none leading-6 transition-colors duration-200 ease-in-out">{{ old('contact') }}</textarea>
                                            </div>
                                            <x-input-error :messages="$errors->get('contact')" class="mt-2" />
                                        </div> --}}

                                        {{-- <div class="p-2 w-full">
                                            <div class="relative">
                                                <input type="checkbox" id="caution" name="caution">注意事項に同意する
                                            </div>
                                            <x-input-error :messages="$errors->get('caution')" class="mt-2" />
                                        </div> --}}

                                        <div class="p-2 w-full">
                                            <button
                                                class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">新規登録する</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>