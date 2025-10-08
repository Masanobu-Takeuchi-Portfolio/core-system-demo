<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            詳細画面
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <section class="text-gray-600 body-font relative">
                        @if (session('message'))
                            <div class="text-center {{ config('constants.common.message_color') }}">
                                {{ session('message') }}
                            </div>
                        @endif
                        <p class="text-center">
                            ユーザーID{{ $user->id }}:&nbsp;{{ $user->name_last }}&nbsp;{{ $user->name_first }}のプロフィール情報
                        </p>
                        <div class="container px-5 mx-auto">
                            <div class="lg:w-1/2 md:w-2/3 mx-auto">
                                <div class="flex flex-wrap -m-2">
                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="name_last" class="leading-7 text-sm text-gray-600">姓</label>
                                            <div
                                                class="w-full border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $user->name_last }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="name_first" class="leading-7 text-sm text-gray-600">名</label>
                                            <div
                                                class="w-full border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $user->name_first }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="name_first" class="leading-7 text-sm text-gray-600">社員番号</label>
                                            <div
                                                class="w-full border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $user->staff_number }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="name_first" class="leading-7 text-sm text-gray-600">所属</label>
                                            <div
                                                class="w-full border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $user->department_id === 1 ? '大阪' : '東京' }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="email"
                                                class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                            <div
                                                class="w-full  border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                {{ $user->email }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="url" class="leading-7 text-sm text-gray-600">交通費URL</label>
                                            @if ($user->url_transportation_expenses)
                                                <div
                                                    class="w-full  border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    {{ $user->url_transportation_expenses }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="p-2 w-full">
                                        <div class="relative">
                                            <label for="url"
                                                class="leading-7 text-sm text-gray-600">スケジュールURL</label>
                                            @if ($user->url_schedule)
                                                <div
                                                    class="w-full  border-b border-gray-300 focus:border-indigo-500 focus:bg-white focus:ring-2 focus:ring-indigo-200 text-base outline-none text-gray-700 py-1 px-3 leading-8 transition-colors duration-200 ease-in-out">
                                                    {{ $user->url_schedule }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <form method="get"
                                        action="{{ route('admin.userinfo.edit', ['id' => $user->id]) }}">
                                        <div class="p-2 w-full">
                                            <button
                                                class="flex mx-auto text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">編集する</button>
                                        </div>
                                    </form>

                                    <form class="mt-40" method="post"
                                        action="{{ route('admin.userinfo.destroy', ['id' => $user->id]) }}"
                                        id="delete_{{ $user->id }}">
                                        @csrf
                                        <div class="p-2 w-full">
                                            <a href="#" data-id="{{ $user->id }}" onclick="deletePost(this)"
                                                class="flex mx-auto text-white bg-pink-500 border-0 py-2 px-8 focus:outline-none hover:bg-pink-600 rounded text-lg">削除
                                                する</a>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
    <!-- 確認メッセージ -->
    <script>
        function deletePost(e) {
            'use strict'
            if (confirm('本当に削除していいですか？')) {
                document.getElementById('delete_' + e.dataset.id).submit()
            }
        }
    </script>
</x-app-layout>
