<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fares', function (Blueprint $table) {
            $table->id();
            // カラムの外部キー制約追加 ※要注意Laravel9から外部キー制約の記述が変更された。9以降は下記が正解。
            // 外部キー制約の->constrained()をつけると親テーブルに存在しないIDは指定できないようにできる。
            $table->foreignId('user_id')->comment('ユーザーid')->constrained()->onDelete('cascade');
            // 年度
            $table->integer('year')->comment('年度')->nullable();
            // 月
            $table->integer('month')->comment('月')->nullable();
            // 交通費データJSON形式
            $table->json('data')->comment('勤務データ')->nullable();
            // 東京_前半合計
            $table->integer('first_total')->comment('東京_前半合計')->nullable();
            // 東京_後半合計
            $table->integer('second_total')->comment('東京_後半合計')->nullable();
            // 大阪_合計
            $table->integer('osaka_total')->comment('大阪_合計')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fares');
    }
};
