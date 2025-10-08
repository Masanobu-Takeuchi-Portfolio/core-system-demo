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
        Schema::table('jobs', function (Blueprint $table) {
            // 東京_総支給額その他カラム追加
            $table->json('tokyo_other_supply')->comment('東京_総支給額_その他')->nullable()->after('osaka_other_allowances');
            // 東京_総控除額その他カラム追加
            $table->json('tokyo_other_subsidy')->comment('東京_総控除額_その他')->nullable()->after('tokyo_other_supply');
            // 大阪_総支給額その他カラム追加
            $table->json('osaka_other_supply')->comment('大阪_総支給額_その他')->nullable()->after('tokyo_other_subsidy');
            // 大阪_総控除額その他カラム追加
            $table->json('osaka_other_subsidy')->comment('大阪_総控除額_その他')->nullable()->after('osaka_other_supply');


            // // 東京_総支給額その他カラム追加
            // $table->string('tokyo_other1')->comment('東京_総支給額_その他1')->after('osaka_other_allowances');
            // $table->string('tokyo_other2')->comment('東京_総支給額_その他2')->after('tokyo_other1');
            // $table->string('tokyo_other3')->comment('東京_総支給額_その他3')->after('tokyo_other2');
            // $table->string('tokyo_other4')->comment('東京_総支給額_その他4')->after('tokyo_other3');
            // $table->string('tokyo_other5')->comment('東京_総支給額_その他5')->after('tokyo_other4');
            // // 東京_総支給額その他カラム名称追加
            // $table->string('tokyo_other_name1')->comment('東京_総支給額_その他名目名1')->after('tokyo_other5');
            // $table->string('tokyo_other_name2')->comment('東京_総支給額_その他名目名2')->after('tokyo_other_name1');
            // $table->string('tokyo_other_name3')->comment('東京_総支給額_その他名目名3')->after('tokyo_other_name2');
            // $table->string('tokyo_other_name4')->comment('東京_総支給額_その他名目名4')->after('tokyo_other_name3');
            // $table->string('tokyo_other_name5')->comment('東京_総支給額_その他名目名5')->after('tokyo_other_name4');
            // // 大阪_総支給額その他カラム追加
            // $table->string('osaka_other1')->comment('大阪_総支給額_その他1')->after('tokyo_other_name5');
            // $table->string('osaka_other2')->comment('大阪_総支給額_その他2')->after('osaka_other1');
            // $table->string('osaka_other3')->comment('大阪_総支給額_その他3')->after('osaka_other2');
            // $table->string('osaka_other4')->comment('大阪_総支給額_その他4')->after('osaka_other3');
            // $table->string('osaka_other5')->comment('大阪_総支給額_その他5')->after('osaka_other4');
            // // 大阪_総支給額その他カラム名称追加
            // $table->string('osaka_other_name1')->comment('大阪_総支給額_その他名目名1')->after('osaka_other5');
            // $table->string('osaka_other_name2')->comment('大阪_総支給額_その他名目名2')->after('osaka_other_name1');
            // $table->string('osaka_other_name3')->comment('大阪_総支給額_その他名目名3')->after('osaka_other_name2');
            // $table->string('osaka_other_name4')->comment('大阪_総支給額_その他名目名4')->after('osaka_other_name3');
            // $table->string('osaka_other_name5')->comment('大阪_総支給額_その他名目名5')->after('osaka_other_name4');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jobs', function (Blueprint $table) {
            $table->dropColumn('tokyo_other_supply');
            $table->dropColumn('tokyo_other_subsidy');
            $table->dropColumn('osaka_other_supply');
            $table->dropColumn('osaka_other_subsidy');
            // $table->dropColumn('tokyo_other1');
            // $table->dropColumn('tokyo_other2');
            // $table->dropColumn('tokyo_other3');
            // $table->dropColumn('tokyo_other4');
            // $table->dropColumn('tokyo_other5');
            // $table->dropColumn('osaka_other1');
            // $table->dropColumn('osaka_other2');
            // $table->dropColumn('osaka_other3');
            // $table->dropColumn('osaka_other4');
            // $table->dropColumn('osaka_other5');
        });
    }
};
