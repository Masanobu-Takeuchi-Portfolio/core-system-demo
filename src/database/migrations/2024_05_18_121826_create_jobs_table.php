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
        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            // カラムの外部キー制約追加 ※要注意Laravel9から外部キー制約の記述が変更された。9以降は下記が正解。
            // 外部キー制約の->constrained()をつけると親テーブルに存在しないIDは指定できないようにできる。
            $table->foreignId('user_id')->comment('ユーザーid')->constrained()->onDelete('cascade');
            // 年度
            $table->integer('year')->comment('年度')->nullable();
            // 月
            $table->integer('month')->comment('月')->nullable();
            // 勤務データJSON形式
            $table->json('data')->comment('勤務データ')->nullable();

            $table->string('diff_total')->comment('差合計')->nullable();
            $table->string('working_days')->comment('出勤日数')->nullable();
            $table->string('overtime_work')->comment('時間外労働')->nullable();
            $table->string('base_salary')->comment('基本給')->nullable();
            $table->string('overtime_charge')->comment('時間外手当')->nullable();
            $table->string('traffic_expenses')->comment('交通費立替金')->nullable();
            $table->string('nomination_fee')->comment('ご指名手当')->nullable();
            $table->string('all_payment')->comment('総支給額')->nullable();
            $table->string('health_insurance')->comment('健康保険')->nullable();
            $table->string('welfare_pension')->comment('厚生年金')->nullable();
            $table->string('unemployment_insurance')->comment('雇用保険')->nullable();
            $table->string('income_tax')->comment('源泉所得税')->nullable();
            $table->string('municipal_tax')->comment('市民税')->nullable();
            $table->string('total_deductions')->comment('控除額合計')->nullable();

            // 大阪専用項目
            $table->string('osaka_deemed_overtime_pay')->comment('大阪_みなし残業代')->nullable();
            $table->string('osaka_traffic_expenses')->comment('大阪_通勤手当')->nullable();
            $table->string('osaka_special_allowance')->comment('大阪_特別手当')->nullable();
            $table->string('osaka_other_allowances')->comment('大阪_その他手当')->nullable();

            $table->text('notes')->comment('備考欄')->nullable();

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
        Schema::dropIfExists('jobs');
    }
};
