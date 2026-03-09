<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 font-bold">
                    {{ __("管理者のホーム画面") }}
                </div>
                <ul class="p-6 list-disc">
                    <li class="mb-2">ユーザー一覧は<a href="{{route('admin.userinfo.index')}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-gray-600 bg-gradient-to-b from-gray-400 via-gray-500 to-gray-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#d1d5db] hover:bg-gradient-to-b hover:from-gray-600 hover:via-gray-600 hover:to-gray-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">ユーザー管理</span>
                        </button>
                    </a></li>
                    <li class="mb-2">出勤簿一覧は<a href="{{route('admin.job.index')}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-blue-600 bg-gradient-to-b from-blue-400 via-blue-500 to-blue-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#93c5fd] hover:from-blue-600 hover:via-blue-600 hover:to-blue-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">勤怠管理</span>
                        </button>
                    </a></li>
                    <li class="mb-2">交通費明細一覧は<a href="{{route('admin.fare.index')}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-teal-600 bg-gradient-to-b from-teal-400 via-teal-500 to-teal-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#5eead4] hover:bg-gradient-to-b hover:from-teal-600 hover:via-teal-600 hover:to-teal-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">交通費管理</span>
                        </button>
                    </a></li>
                    <li class="mb-2">物品発注一覧は<a href="{{route('admin.item.index')}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-orange-600 bg-gradient-to-b from-orange-400 via-orange-500 to-orange-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#fdba74] active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">物品発注管理</span>
                        </button>
                    </a></li>
                    <li class="mb-2">スクレイピングは<a href="{{route('admin.scraping.index')}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-indigo-600 bg-gradient-to-b from-indigo-400 via-indigo-500 to-indigo-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#a5b4fc] hover:from-indigo-600 hover:via-indigo-600 hover:to-indigo-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">スクレイピング</span>
                        </button>
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</x-app-layout>
