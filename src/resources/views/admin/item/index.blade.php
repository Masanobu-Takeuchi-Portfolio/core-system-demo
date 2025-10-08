<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            物品発注一覧
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <form class="mb-8" method="get" action="{{ route('admin.item.index') }}">
                        <div class="flex flex-wrap items-end -m-2">
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="name" class="leading-7 text-sm text-gray-600">姓</label>
                                    <input class="w-full" type="text" name="search_name_last"
                                        @if (isset($search_name_last)) value={{ $search_name_last }} @endif
                                        placeholder="姓">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="email" class="leading-7 text-sm text-gray-600">名</label>
                                    <input class="w-full" type="text" name="search_name_first"
                                        @if (isset($search_name_first)) value={{ $search_name_first }} @endif
                                        placeholder="名">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <label for="email" class="leading-7 text-sm text-gray-600">メールアドレス</label>
                                    <input class="w-full" type="text" name="search_email"
                                        @if (isset($search_email)) value={{ $search_email }} @endif
                                        placeholder="メールアドレス">
                                </div>
                            </div>
                            <div class="p-2 md:w-1/4 w-1/2">
                                <div class="relative">
                                    <button
                                        class="text-white bg-indigo-500 border-0 py-2 px-8 focus:outline-none hover:bg-indigo-600 rounded text-lg">検索する</button>
                                </div>
                            </div>
                        </div>

                    </form>

                    {{ $items->links() }}
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
                                        年度</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        月</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        所属</th>
                                    <th
                                        class="px-4 py-3 title-font tracking-wider font-medium text-gray-900 text-sm bg-gray-100">
                                        詳細</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($items as $item)
                                    <tr>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $item->id }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            {{ $item->name_last }}&nbsp;{{ $item->name_first }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $item->email }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $item->year }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $item->month }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">{{ $item->department }}</td>
                                        <td class="border-t-2 border-gray-200 px-4 py-3">
                                            <a href="{{ route('admin.item.edit', ['id' => $item->id, 'year' => $item->year, 'month' => $item->month]) }}"
                                                class="text-blue-500">詳細を見る</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{ $items->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
