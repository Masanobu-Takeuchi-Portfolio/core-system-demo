<?php

return [
    // 共通の定数
    'common' => [
        // 正常処理時のメッセージのカラー
        'message_color' => 'text-white bg-indigo-500 text-center font-bold',
        // エラー処理時のメッセージのカラー
        // 'error_message_color' => 'text-white bg-fuchsia-300',
        'error_message_color' => 'text-white bg-red-600 text-center font-bold',
    ],
    // 管理者機能の定数
    'admin' => [
        'message' => [
            'job_create' => "勤怠を登録しました。",
            'user_update' => ':name_last :name_firstのユーザー情報を更新しました。'
        ],
        'edit_flg' => env('ADMIN_EDIT_FLG', false),
    ],
    // 一般ユーザー機能の定数
    'user' => [
        'message' => [
            'job_create' => '勤怠を登録しました。',
            'job_update' => '勤怠を更新しました。',
            'fare_create' => '交通費申請を登録しました。',
            'fare_update' => '交通費申請を更新しました。',
            'item_create' => '物品発注を登録しました。',
            'item_update' => '物品発注を更新しました。'
        ]
    ],
    // // 一般ユーザー機能の定数
    // 'department' => [
    //     1 => '大阪',
    //     2 => '東京',
    // ]
];
