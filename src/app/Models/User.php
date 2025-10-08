<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [ // DBにカラムを複数代入するためにはここにカラム名を追加する必要あり。
        'name_last',
        'name_first',
        'staff_number',
        'department_id',
        'email',
        'password',
        'url_transportation_expenses',
        'url_schedule',
        // 住所、性別、電話番号、所属部署、役職、ダミデータフラグ
        'dummy',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // ローカルスコープ。第一引数の$queryは使わなくても必須らしい謎だ。
    public function scopeSearch($query, $request)
    {
        if (isset($request->search_name_last)) {
            $search_split = mb_convert_kana($request->search_name_last, 's'); // 全角スペースを半角
            $search_split2 = preg_split('/[\s]+/', $search_split); //空白で区切る
            foreach ($search_split2 as $value) {
                $query->where('name_last', 'like', '%' . $value . '%');
                //検索ワードに「渚」と入力すると'name'に「渚」が含まれるモノと’title’の内容に「渚」が含まれているモノを取得する検索
                //$query->where('name', 'like', '%' .$value. '%')->orWhere('title', 'like', '%' .$value. '%');
            }
        }
        if (isset($request->search_name_first)) {
            $search_split = mb_convert_kana($request->search_name_first, 's'); // 全角スペースを半角
            $search_split2 = preg_split('/[\s]+/', $search_split); //空白で区切る
            foreach ($search_split2 as $value) {
                $query->where('name_first', 'like', '%' . $value . '%');
            }
        }
        if (isset($request->search_email)) {
            $search_split = mb_convert_kana($request->search_email, 's'); // 全角スペースを半角
            $search_split2 = preg_split('/[\s]+/', $search_split); //空白で区切る
            foreach ($search_split2 as $value) {
                $query->where('email', 'like', '%' . $value . '%');
            }
        }
        return $query;
    }
}
