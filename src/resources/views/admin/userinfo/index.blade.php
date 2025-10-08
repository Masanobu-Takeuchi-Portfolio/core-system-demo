<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ユーザー一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form class="mb-8" method="get" action="{{ route('admin.userinfo.index') }}">
                        <div class="flex flex-wrap items-end -m-2">
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="name" class="leading-7 text-sm text-gray-600">姓</label>
                                    <input class="w-full" type="text" name="search_name_last"
                                        @if(isset($search_name_last)) value={{ $search_name_last }} @endif
                                        placeholder="姓">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="email" class="leading-7 text-sm text-gray-600">名</label>
                                    <input class="w-full" type="text" name="search_name_first"
                                        @if(isset($search_name_first)) value={{ $search_name_first }} @endif
                                        placeholder="名">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                    <input class="w-full" type="text" name="search_email" @if(isset($search_email))
                                        value={{ $search_email }} @endif placeholder="メールアドレス">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <button
                                        class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">検索する</button>
                                </div>
                            </div>
                        </div>
                        {{-- <input type="text" name="search_name_last" @if(isset($search_name_last)) value={{
                            $search_name_last }} @endif placeholder="姓">
                        <input type="text" name="search_name_first" @if(isset($search_name_first)) value={{
                            $search_name_first }} @endif placeholder="名">
                        <input type="text" name="search_email" @if(isset($search_email)) value={{ $search_email }}
                            @endif placeholder="メールアドレス">
                        <button
                            class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">検索する</button>
                        --}}

                    </form>
                    <a href="{{ route('admin.userinfo.create') }}" class="block mb-8">
                        <button
                            class="text-white bg-blue-500 border-0 py-2 px-8 focus:outline-none hover:bg-blue-600 rounded text-lg">新規登録</button>
                    </a>
                    {{ $users->links() }}
                    <div class="w-full mx-auto overflow-auto">
                        <table class="table-auto w-full text-left whitespace-no-wrap">
                            <thead>
                                <tr>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100 rounded-tl rounded-bl">
                                        ID</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        氏名</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        メールアドレス</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        交通費リンク</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        スケジュールリンク</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        登録日</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($users as $user)
                                <tr>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">{{ $user->id }}</td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">
                                        {{ $user->name_last }}&nbsp;{{ $user->name_first }}</td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">{{ $user->email }}</td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">
                                        <a href="{{ $user->url_transportation_expenses }}" target="_blank">
                                            <button class="bg-yellow-500 hover:opacity-75 p-1 rounded font-bold
                                            transition duration-300 text-white">
                                                リンク先
                                            </button>
                                        </a>
                                    </td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">
                                        <a href="{{ $user->url_schedule }}" target="_blank">
                                            <button class="bg-yellow-500 hover:opacity-75 p-1 rounded font-bold
                                            transition duration-300 text-white">
                                                リンク先
                                            </button>
                                        </a>
                                    </td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3 text-lg text-gray-900">
                                        {{ $user->created_at }}</td>
                                    <td class="border-t-2 border-gray-200 px-4 py-3">
                                        <a href="{{ route('admin.userinfo.show', ['id' => $user->id]) }}"
                                            class="text-blue-500">詳細を見る</a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>