<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('スクレイピング結果') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- 戻るボタン --}}
            <div class="mb-4">
                <a href="{{ route('admin.scraping.index') }}"
                   class="inline-flex items-center text-indigo-600 hover:text-indigo-800">
                    &larr; スクレイピング画面に戻る
                </a>
            </div>

            @if(isset($result['success']) && $result['success'])
                {{-- 成功時 --}}

                {{-- ページ情報 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">ページ情報</h3>
                        <table class="w-full text-sm">
                            <tbody>
                                <tr class="border-b">
                                    <th class="py-2 pr-4 text-left text-gray-600 w-40">URL</th>
                                    <td class="py-2">
                                        <a href="{{ $result['url'] }}" target="_blank" class="text-blue-500 hover:underline break-all">
                                            {{ $result['url'] }}
                                        </a>
                                    </td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 pr-4 text-left text-gray-600">タイトル</th>
                                    <td class="py-2">{{ $result['title'] ?? '-' }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 pr-4 text-left text-gray-600">メタディスクリプション</th>
                                    <td class="py-2">{{ $result['meta_description'] ?? '-' }}</td>
                                </tr>
                                <tr class="border-b">
                                    <th class="py-2 pr-4 text-left text-gray-600">CSSセレクタ</th>
                                    <td class="py-2">
                                        @if($selector)
                                            <code class="bg-gray-100 px-2 py-1 rounded">{{ $selector }}</code>
                                        @else
                                            <span class="text-gray-400">未指定（自動取得）</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th class="py-2 pr-4 text-left text-gray-600">取得件数</th>
                                    <td class="py-2">{{ $result['elements_count'] ?? 0 }}件</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- スクリーンショット --}}
                @if(!empty($result['screenshot']))
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">スクリーンショット</h3>
                        <div class="border rounded-lg overflow-hidden">
                            <img src="data:image/png;base64,{{ $result['screenshot'] }}" alt="スクリーンショット" class="w-full">
                        </div>
                    </div>
                </div>
                @endif

                {{-- 取得要素一覧 --}}
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-bold mb-4">取得要素一覧</h3>

                        @if(!empty($result['elements']) && count($result['elements']) > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm border-collapse">
                                    <thead>
                                        <tr class="bg-gray-50">
                                            <th class="border px-3 py-2 text-left w-10">#</th>
                                            <th class="border px-3 py-2 text-left w-20">タグ</th>
                                            <th class="border px-3 py-2 text-left">テキスト</th>
                                            <th class="border px-3 py-2 text-left">href / src</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result['elements'] as $index => $element)
                                            <tr class="{{ $index % 2 === 0 ? 'bg-white' : 'bg-gray-50' }}">
                                                <td class="border px-3 py-2 text-gray-500">{{ $index + 1 }}</td>
                                                <td class="border px-3 py-2">
                                                    <code class="bg-gray-100 px-1 rounded text-xs">{{ $element['tag'] }}</code>
                                                </td>
                                                <td class="border px-3 py-2 break-all">
                                                    {{ \Illuminate\Support\Str::limit($element['text'] ?? '', 200) }}
                                                </td>
                                                <td class="border px-3 py-2 break-all text-blue-500">
                                                    @if(!empty($element['href']))
                                                        <a href="{{ $element['href'] }}" target="_blank" class="hover:underline">
                                                            {{ \Illuminate\Support\Str::limit($element['href'], 100) }}
                                                        </a>
                                                    @elseif(!empty($element['src']))
                                                        {{ \Illuminate\Support\Str::limit($element['src'], 100) }}
                                                    @else
                                                        <span class="text-gray-400">-</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-gray-500">取得できた要素はありません。</p>
                        @endif
                    </div>
                </div>

            @else
                {{-- エラー時 --}}
                <div class="bg-red-50 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold text-red-700 mb-2">エラーが発生しました</h3>
                        <p class="text-red-600">{{ $result['error'] ?? 'スクレイピング中に不明なエラーが発生しました。' }}</p>
                        <div class="mt-4">
                            <a href="{{ route('admin.scraping.index') }}"
                               class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700">
                                再度試す
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
