<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            編集画面
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">
                        <p class="text-center">ユーザーID{{ $user->id }}:&nbsp;{{ $user->name_last }}&nbsp;{{
                            $user->name_first }}のプロフィール編集</p>
                        <form method="post" action="{{ route('admin.userinfo.update', ['id' => $user->id]) }}">
                            @csrf
                            <div class="container px-5 mx-auto">
                                <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                    <div class="flex flex-wrap -m-2">
                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_last" class="leading-7 text-sm text-gray-600">姓</label>
                                                <input type="text" id="name_last" name="name_last"
                                                    value="{{ old('name_last', $user->name_last) }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('name_last')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="name_first"
                                                    class="leading-7 text-sm text-gray-600">姓</label>
                                                <input type="text" id="name_first" name="name_first"
                                                    value="{{ old('name_first', $user->name_first) }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('name_first')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="email"
                                                    class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                                <input type="email" id="email" name="email"
                                                    value="{{ old('email', $user->email) }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="password"
                                                    class="leading-7 text-sm text-gray-600">パスワード</label>
                                                <input type="password" id="password" name="password"
                                                    value="{{ old('password') }}"
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
                                                <input type="url" id="url_transportation_expenses"
                                                    name="url_transportation_expenses"
                                                    value="{{ old('url_transportation_expenses', $user->url_transportation_expenses) }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('url_transportation_expenses')"
                                                class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <div class="relative">
                                                <label for="url_schedule"
                                                    class="leading-7 text-sm text-gray-600">スケジュールURL</label>
                                                <input type="url" id="url_schedule" name="url_schedule"
                                                    value="{{ old('url_schedule', $user->url_schedule) }}"
                                                    class="w-full bg-gray-100 bg-opacity-50 rounded border border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                            </div>
                                            <x-input-error :messages="$errors->get('url_schedule')" class="mt-2" />
                                        </div>

                                        <div class="p-2 w-full">
                                            <button
                                                class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">更新する</button>
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