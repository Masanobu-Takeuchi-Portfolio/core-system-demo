@extends('errors::minimal')

@section('title', __('ページの有効期限切れ'))
@section('code', '419')
@section('message', __('ページの有効期限が切れましたので下記のログインページを再度表示してください。'))
 {{-- <ul class="p-6 list-disc">
    <li class="mb-2">一般ユーザーのログイン画面は<a href="{{route('user.login')}}" class="text-blue-500">コチラから
        <button class="group flex h-10 items-center justify-center rounded-md border border-gray-600 bg-gradient-to-b from-gray-400 via-gray-500 to-gray-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#d1d5db] hover:bg-gradient-to-b hover:from-gray-600 hover:via-gray-600 hover:to-gray-600 active:[box-shadow:none]">
            <span class="block group-active:[transform:translate3d(0,1px,0)]">ユーザーのログイン</span>
        </button>
    </a></li>
    <li class="mb-2">管理者のログイン画面は<a href="{{route('admin.login')}}" class="text-blue-500">コチラから
        <button class="group flex h-10 items-center justify-center rounded-md border border-blue-600 bg-gradient-to-b from-blue-400 via-blue-500 to-blue-600 px-4 text-neutral-50 shadow-[inset_0_1px_0px_0px_#93c5fd] hover:from-blue-600 hover:via-blue-600 hover:to-blue-600 active:[box-shadow:none]">
            <span class="block group-active:[transform:translate3d(0,1px,0)]">管理者のログイン</span>
        </button>
    </a></li>
 </ul> --}}