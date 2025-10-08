<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactForm extends Model
{
    use HasFactory;

    protected $fillable = [ // DBにカラムを複数代入するためにはここにカラム名を追加する必要あり。
        'name',
        'title',
        'email',
        'url',
        'gender',
        'age',
        'contact'
    ];

    // ローカルスコープ。第一引数の$queryは使わなくても必須らしい謎だ。
    public function scopeSearch($query, $search)
    {
        if ($search !== null) {
            $search_split = mb_convert_kana($search, 's'); // 全角スペースを半角
            $search_split2 = preg_split('/[\s]+/', $search_split); //空白で区切る
            foreach ($search_split2 as $value) {
                $query->where('name', 'like', '%' . $value . '%');
                //検索ワードに「渚」と入力すると'name'に「渚」が含まれるモノと’title’の内容に「渚」が含まれているモノを取得する検索
                //$query->where('name', 'like', '%' .$value. '%')->orWhere('title', 'like', '%' .$value. '%');
            }
        }
        return $query;
    }
}
