<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <!-- jQuery UIのCSS -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/themes/base/jquery-ui.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        @if (auth('admin')->user())
        @include('layouts.admin-navigation')
        @elseif (auth('owners')->user())
        @include('layouts.owner-navigation')
        @elseif (auth('users')->user())
        @include('layouts.user-navigation')
        @endif

        <!-- Page Heading -->
        @if (isset($header))
        <header class="bg-white shadow">
            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                {{ $header }}
            </div>
        </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>
    <!-- jQuery本体 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- jQuery UI -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
    <!-- 日本語化ファイル -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.10.4/i18n/jquery.ui.datepicker-ja.min.js"></script>
    <script>
        $('.difftime').on('change',function(){
            const id = this.id.split('_')[2];
            const target = $("#" + this.id).val();
            if (target === 'empty' || target == '99') {
                $("#start_time_" + id).val("empty");
                $("#end_time_" + id).val("empty");
                $("#rest_time_" + id).val(99);

                $("#worktime_" + id).html('0:00');
                $("#difference_" + id).html('0:00');
                $("#calc_difference_" + id).val(0);
            }

            const worktimeTarget = $("#worktime_" + id);
            const differenceTarget = $("#difference_" + id);
            const calcDifferenceTarget = $("#calc_difference_" + id);

            if ($("#start_time_" + id).val() !== 'empty' && $("#end_time_" + id).val() !== 'empty' && 
                $("#rest_time_" + id).val() !== '99') {
                let t1 = new Date("2024-08-11 " + $("#start_time_" + id).val());
                let t2 = new Date("2024-08-11 " + $("#end_time_" + id).val());

                let diff = t2.getTime() - t1.getTime();

                //HH部分取得
                let diffHour = (diff / (1000 * 60 * 60)) - parseFloat($("#rest_time_" + id).val());
                let differenceTime = diffHour - 8;
                let minute = ':30';
                if ((diffHour * 10) % 10 === 0) {
                    minute = ':00';
                }
                $(worktimeTarget).html(parseInt(diffHour) + minute);
                minute = ':30';
                if ((differenceTime * 10) % 10 === 0) {
                    minute = ':00';
                }

                // 差合計計算用を更新
                $(calcDifferenceTarget).val(differenceTime);
                if (differenceTime === -0.5) {
                    $(differenceTarget).html('-0' + minute);
                } else {
                    $(differenceTarget).html(Math.trunc(differenceTime) + minute);
                }                
            }

            // 差合計の再計算
            const day_num = parseInt($("#loop_count").val());
            //console.log(day_num);
            let calc_diff_total = 0;
            let arr_difftotal = [];
            let tmp_difftotal_h = 0;
            for (var i=0; i<= day_num; i++) {
                tmp_difftotal_h += parseFloat($("#calc_difference_" + i).val());
            }

            minute = ':00';
            if ((tmp_difftotal_h * 10) % 10 !== 0) {
                // minute = ':00';
                minute = ':30';
            }
            $("#calc_diff_total").val(tmp_difftotal_h);
            if (tmp_difftotal_h === -0.5) {
                $("#diff_total").html('-0:30');
            } else {
                $("#diff_total").html(parseInt(tmp_difftotal_h) + minute);
            }
            
        });
        $(function () {
            $('.datepic').datepicker(
                { 
                    //altField: '#result',  // 入力結果の表示箇所
                    dateFormat: 'm月d日',
                    // dateFormat: 'yyyy-MM-dd',
                    // regional: 'ja'
                    // changeYear: true,
                    // changeMonth: true 
                }
            );
            // 大阪交通費明細の入力項目追加ボタンの実装
            $(document).on('click', '#add-field-btn', function() {
                let next_input_num =  parseInt($("#input_num").val());
                $("#input_num").val(next_input_num + 1);
                const target = $("#input_box_" + next_input_num);
                target.removeClass('hidden');
            });

            // 物品発注の入力項目追加ボタンの実装
            $(document).on('click', '#item-add-field-btn', function() {
                // datepickerを更新するために破棄する
                $(".datepic").datepicker("destroy");
                let addForm = $('.addForm');
                addForm = addForm.prop("outerHTML").replace("addForm", "");
                addForm = addForm.replace("hidden", "");
                $('.form-fields-container').append(addForm);
                 // // datepickerの更新
                $('.datepic').datepicker(
                    { 
                        dateFormat: 'm月d日',
                    }
                );
                $("#item-add-field-btn").attr('class', 'hidden');
             });
        });    
    </script>
</body>

</html>