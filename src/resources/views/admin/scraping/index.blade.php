<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('スクレイピング') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Puppeteer スクレイピング</h3>
                    <p class="mb-6 text-gray-600">
                        対象のURLを入力し、スクレイピングを実行します。CSSセレクタを指定すると、特定の要素のみ取得できます。
                    </p>

                    <form method="POST" action="{{ route('admin.scraping.execute') }}">
                        @csrf

                        {{-- URL入力 --}}
                        <div class="mb-4">
                            <label for="url" class="block text-sm font-medium text-gray-700 mb-1">
                                URL <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="url"
                                name="url"
                                id="url"
                                value="{{ old('url') }}"
                                placeholder="https://example.com"
                                required
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            @error('url')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CSSセレクタ入力 --}}
                        <div class="mb-6">
                            <label for="selector" class="block text-sm font-medium text-gray-700 mb-1">
                                CSSセレクタ（任意）
                            </label>
                            <input
                                type="text"
                                name="selector"
                                id="selector"
                                value="{{ old('selector') }}"
                                placeholder="例: h1, .class-name, #id-name, a[href]"
                                class="w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            <p class="mt-1 text-sm text-gray-500">
                                未入力の場合は、見出し（h1〜h3）、リンク（a）、画像（img）を自動取得します。
                            </p>
                            @error('selector')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 実行ボタン --}}
                        <div>
                            <button
                                type="submit"
                                class="group flex h-10 items-center justify-center rounded-md border border-indigo-600 bg-gradient-to-b from-indigo-400 via-indigo-500 to-indigo-600 px-6 text-neutral-50 shadow-[inset_0_1px_0px_0px_#a5b4fc] hover:from-indigo-600 hover:via-indigo-600 hover:to-indigo-600 active:[box-shadow:none]"
                            >
                                <span class="block group-active:[transform:translate3d(0,1px,0)]">
                                    スクレイピング実行
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- 使い方説明 --}}
            <div class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h4 class="font-bold mb-3">使い方</h4>
                    <div class="text-sm text-gray-600 space-y-2">
                        <p><strong>1. URL入力:</strong> スクレイピング対象のWebページのURLを入力してください。</p>
                        <p><strong>2. CSSセレクタ（任意）:</strong> 特定の要素のみ取得したい場合に指定してください。</p>
                        <ul class="list-disc ml-6 mt-1 space-y-1">
                            <li><code class="bg-gray-100 px-1 rounded">h1</code> - h1タグの要素を取得</li>
                            <li><code class="bg-gray-100 px-1 rounded">.class-name</code> - クラス名で要素を取得</li>
                            <li><code class="bg-gray-100 px-1 rounded">#id-name</code> - ID名で要素を取得</li>
                            <li><code class="bg-gray-100 px-1 rounded">a[href]</code> - リンク付きのaタグを取得</li>
                            <li><code class="bg-gray-100 px-1 rounded">table tr</code> - テーブルの行を取得</li>
                        </ul>
                        <p><strong>3. 実行:</strong> ボタンを押すとスクレイピングが実行され、結果が表示されます。</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
