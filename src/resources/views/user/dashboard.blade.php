<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-2 text-gray-900 font-bold">
                    {{ __("一般ユーザーのホーム画面") }}
                </div>
                @php
                    $JOB_SEPARATER = 10; // 出勤簿の次月開始日
                    $now = Carbon\CarbonImmutable::now();
                    $calendar = (object) [];
                    $calendar->year = $now->year;
                    //$calendarOldyear = $now->year;
                    if ($now->day <= $JOB_SEPARATER) {
                        // 次月を跨いでいるので、前月が当月分出勤簿の月となる。
                        $calendar->month = $now->month;
                    } else {
                        // 月を跨いだ 11日以降 次月の勤怠となる。
                        $calendar->month = $now->addMonth(1)->month;
                        //echo $now->year;
                        //echo $now->month;
                        if ($now->month === 12 && $calendar->month === 1) {
                            $calendar->year++;
                        }
                    }
                    
                @endphp

                <ul class="p-6 list-disc">
                    <li class="mb-2">今月の出勤簿の入力は<a href="{{route('user.job.index.search', ['year' => $calendar->year, 'month' => $calendar->month])}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-blue-600 bg-gradient-to-b from-blue-400 via-blue-500 to-blue-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#93c5fd] hover:from-blue-600 hover:via-blue-600 hover:to-blue-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">出勤簿入力</span>
                        </button>
                    </a></li>
                    <li class="mb-2">今月の交通費の入力は<a href="{{route('user.fare.index.search', ['year' => $calendar->year, 'month' => $calendar->month])}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-teal-600 bg-gradient-to-b from-teal-400 via-teal-500 to-teal-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#5eead4] hover:bg-gradient-to-b hover:from-teal-600 hover:via-teal-600 hover:to-teal-600 active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">交通費入力</span>
                        </button>
                    </a></li>
                    <li class="mb-2">今月の物品発注の入力は<a href="{{route('user.item.index.search', ['year' => $calendar->year, 'month' => $calendar->month])}}" class="text-blue-500">コチラから
                        <button class="group flex h-10 items-center justify-center rounded-md border border-orange-600 bg-gradient-to-b from-orange-400 via-orange-500 to-orange-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#fdba74] active:[box-shadow:none]">
                            <span class="block group-active:[transform:translate3d(0,1px,0)]">物品発注入力</span>
                        </button>
                    </a></li>
                </ul>
                <p class="p-2">スマホの場合はハンバーガーメニュー内から各ページに移動できます。</p>
            </div>
        </div>
    </div>
</x-app-layout>