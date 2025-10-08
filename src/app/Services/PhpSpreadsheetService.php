<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Models\Job;
use App\Models\fare as Fare;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

const TEMPLATE_KINTAI_TOKYO = "template_kintai_tokyo.xlsx";
const TEMPLATE_KINTAI_OSAKA = "template_kintai_osaka.xlsx";
const TEMPLATE_FARE_TOKYO = "template_fare_tokyo.xlsx";
const TEMPLATE_FARE_OSAKA = "template_fare_osaka.xlsx";
// 出勤簿（東京）データ行のスタートセル番号
const EXPORT_DATE_START_ROW = 8;
// 空欄カラムの値
const EMPTY_DATE = 'empty';
const EMPTY_REST = 99;

const FARE_SEPARATER = 10; // 出勤簿の次月開始日
const FARE_INPUT_ROW_NUM_TOKYO = 6; // 東京は1日の入力枠が6枠
const FARE_TRAFFIC_LIST = [
    'train' => '電車',
    'bus' => 'バス',
    'parking' => '駐車場',
    '' => '',
];

const FARE_WAY_LIST = [
    'one' => '片道',
    'round' => '往復',
    '' => '',
];

// 出勤簿（大阪）データ行のスタートセル番号
//const EXPORT_DATE_START_ROW_OSAKA = 8;

class PhpSpreadsheetService
{

    /**
     * Excelファイルを出力.
     *
     * @return void
     */
    public function export($job_id): void
    {
        $job = Job::find($job_id);
        $user = Job::find($job_id)->user;
        $job_date = isset($job) ? json_decode($job->data, true) : array();


        if ($user->department_id === 2) { // 東京スタッフの場合
            $file = Storage::path(TEMPLATE_KINTAI_TOKYO);
            $tokyo_other_supply = isset($job->tokyo_other_supply) ? json_decode($job->tokyo_other_supply, true) : array();
            $tokyo_other_subsidy = isset($job->tokyo_other_subsidy) ? json_decode($job->tokyo_other_subsidy, true) : array();
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

            // ユーザー情報記載
            $this->spreadsheet->getActiveSheet()->setCellValue('C4', $user->name_last . ' ' . $user->name_first);
            $this->spreadsheet->getActiveSheet()->setCellValue('T4', Carbon::parse($job_date[0]["date"])->format('n/d'));
            $this->spreadsheet->getActiveSheet()->setCellValue('Z4', Carbon::parse(end($job_date)["date"])->format('n/d'));

            // 勤怠の記載
            $row_num = EXPORT_DATE_START_ROW;
            //$count = 0;
            foreach ($job_date as $k => $v) {
                $this->spreadsheet->getActiveSheet()->setCellValue('A' . $row_num, Carbon::parse($v['date'])->format('j') . '日');
                $this->spreadsheet->getActiveSheet()->setCellValue('C' . $row_num, Carbon::parse($v['date'])->isoFormat('ddd'));
                $this->spreadsheet->getActiveSheet()->setCellValue('E' .  $row_num, $v['start_time'] !== EMPTY_DATE ? $v['start_time'] : '');
                $this->spreadsheet->getActiveSheet()->setCellValue('G' . $row_num, $v['end_time'] !== EMPTY_DATE ? $v['end_time'] : '');
                $minute = ($v['rest'] * 10) % 10 === 0 ? ':00' : ':30';
                $this->spreadsheet->getActiveSheet()->setCellValue('I' . $row_num, $v['rest'] !== EMPTY_REST ? floor($v['rest']) . $minute : '');
                $minute = ($v['worktime'] * 10) % 10 === 0 ? ':00' : ':30';
                $this->spreadsheet->getActiveSheet()->setCellValue('K' . $row_num, $v['start_time'] !== EMPTY_DATE ? floor($v['worktime']) . $minute : '');
                $minute = ($v['difference'] * 10) % 10 === 0 ? ':00' : ':30';
                $this->spreadsheet->getActiveSheet()->setCellValue('L' . $row_num, $v['start_time'] !== EMPTY_DATE ? floor($v['difference']) . $minute : '');
                $this->spreadsheet->getActiveSheet()->setCellValue('M' . $row_num, $v['place']);

                $row_num += 1;
            }

            // 出勤日数
            $this->spreadsheet->getActiveSheet()->setCellValue('X7', $job["working_days"] . '日');
            // 時間外労働
            $this->spreadsheet->getActiveSheet()->setCellValue('X8', $job["overtime_work"] . '時間');
            // 基本給
            $this->spreadsheet->getActiveSheet()->setCellValue('X10', $job["base_salary"] . '円');
            // 時間外手当
            $this->spreadsheet->getActiveSheet()->setCellValue('X11', $job["overtime_charge"] . '円');
            // ご指名手当
            $this->spreadsheet->getActiveSheet()->setCellValue('X13', $job["nomination_fee"] . '円');
            // 支給その他項目表示
            $supply_row_num = 14;
            $count = 0;
            foreach ($tokyo_other_supply as $k => $v) {
                $koujo_row_key = $supply_row_num + $count;
                $this->spreadsheet->getActiveSheet()->setCellValue('T' . $koujo_row_key, $v["tokyo_other_name"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('X' . $koujo_row_key, $v["tokyo_other"] . '円');

                $count++;
            }
            // 総支給額
            $this->spreadsheet->getActiveSheet()->setCellValue('X19', $job["all_payment"] . '円');

            // 健康保険
            $this->spreadsheet->getActiveSheet()->setCellValue('X21', $job["health_insurance"] . '円');
            // 厚生年金
            $this->spreadsheet->getActiveSheet()->setCellValue('X22', $job["welfare_pension"] . '円');
            // 雇用保険
            $this->spreadsheet->getActiveSheet()->setCellValue('X23', $job["unemployment_insurance"] . '円');
            // 源泉所得税
            $this->spreadsheet->getActiveSheet()->setCellValue('X24', $job["income_tax"] . '円');
            // 市民税
            $this->spreadsheet->getActiveSheet()->setCellValue('X25', $job["municipal_tax"] . '円');
            // 控除その他項目表示
            $koujo_row_num = 26;
            $count = 0;
            foreach ($tokyo_other_subsidy as $k => $v) {
                $koujo_row_key = $koujo_row_num + $count;
                $this->spreadsheet->getActiveSheet()->setCellValue('T' . $koujo_row_key, $v["tokyo_other_subsidy_name"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('X' . $koujo_row_key, $v["tokyo_other_subsidy"] . '円');

                $count++;
            }
            // 控除額合計
            $this->spreadsheet->getActiveSheet()->setCellValue('X31', $job["total_deductions"] . '円');
            // 備考欄
            //セル内改行指定
            $this->spreadsheet->getActiveSheet()->getStyle('T31:T36')->getAlignment()->setWrapText(true);
            $this->spreadsheet->getActiveSheet()->setCellValue('T33', "【備考欄】\n" . $job["notes"]);
            // 差合計
            $minute = ($job["diff_total"] * 10) % 10 === 0 ? ':00' : ':30';
            $this->spreadsheet->getActiveSheet()->setCellValue('AA40', floor($job["diff_total"]) . $minute);
        } else { // 大阪スタッフの場合
            $file = Storage::path(TEMPLATE_KINTAI_OSAKA);
            $osaka_other_supply = isset($job->osaka_other_supply) ? json_decode($job->osaka_other_supply, true) : array();
            $osaka_other_subsidy = isset($job->osaka_other_subsidy) ? json_decode($job->osaka_other_subsidy, true) : array();
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

            // ユーザー情報記載
            $this->spreadsheet->getActiveSheet()->setCellValue('D4', $user->name_last . ' ' . $user->name_first);
            $this->spreadsheet->getActiveSheet()->setCellValue('S4', Carbon::parse($job_date[0]["date"])->format('n/d'));
            $this->spreadsheet->getActiveSheet()->setCellValue('Y4', Carbon::parse(end($job_date)["date"])->format('n/d'));

            // 勤怠の記載
            $row_num = EXPORT_DATE_START_ROW;
            //$count = 0;
            foreach ($job_date as $k => $v) {
                $this->spreadsheet->getActiveSheet()->setCellValue('A' . $row_num, Carbon::parse($v['date'])->format('j') . '日');
                $this->spreadsheet->getActiveSheet()->setCellValue('D' . $row_num, Carbon::parse($v['date'])->isoFormat('ddd'));
                $this->spreadsheet->getActiveSheet()->setCellValue('F' . $row_num, $v['start_time'] !== EMPTY_DATE ? $v['start_time'] : '');
                $this->spreadsheet->getActiveSheet()->setCellValue('I' . $row_num, $v['end_time'] !== EMPTY_DATE ? $v['end_time'] : '');
                $this->spreadsheet->getActiveSheet()->setCellValue('L' . $row_num, $v['place']);

                $row_num += 1;
            }

            // 出勤日数
            $this->spreadsheet->getActiveSheet()->setCellValue('X7', $job["working_days"] . '日');
            // 時間外労働
            $this->spreadsheet->getActiveSheet()->setCellValue('X8', $job["overtime_work"] . '時間');
            // 基本給
            $this->spreadsheet->getActiveSheet()->setCellValue('X10', $job["base_salary"] . '円');
            // みなし残業代
            $this->spreadsheet->getActiveSheet()->setCellValue('X11', $job["osaka_deemed_overtime_pay"] . '円');
            // 通勤手当
            $this->spreadsheet->getActiveSheet()->setCellValue('X12', $job["osaka_traffic_expenses"]  . '円');
            // 特別手当
            $this->spreadsheet->getActiveSheet()->setCellValue('X13', $job["osaka_special_allowance"] . '円');
            // その他手当
            $this->spreadsheet->getActiveSheet()->setCellValue('X14', $job["osaka_other_allowances"] . '円');
            // 支給その他項目表示
            $supply_row_num = 15;
            $count = 0;
            foreach ($osaka_other_supply as $k => $v) {
                $koujo_row_key = $supply_row_num + $count;
                $this->spreadsheet->getActiveSheet()->setCellValue('T' . $koujo_row_key, $v["osaka_other_name"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('X' . $koujo_row_key, $v["osaka_other"] . '円');

                $count++;
            }
            // 総支給額
            $this->spreadsheet->getActiveSheet()->setCellValue('X20', $job["all_payment"] . '円');

            // 健康保険
            $this->spreadsheet->getActiveSheet()->setCellValue('X22', $job["health_insurance"] . '円');
            // 厚生年金
            $this->spreadsheet->getActiveSheet()->setCellValue('X23', $job["welfare_pension"] . '円');
            // 雇用保険
            $this->spreadsheet->getActiveSheet()->setCellValue('X24', $job["unemployment_insurance"] . '円');
            // 源泉所得税
            $this->spreadsheet->getActiveSheet()->setCellValue('X25', $job["income_tax"] . '円');
            // 市民税
            $this->spreadsheet->getActiveSheet()->setCellValue('X26', $job["municipal_tax"] . '円');
            // 控除その他項目表示
            $koujo_row_num = 27;
            $count = 0;
            foreach ($osaka_other_subsidy as $k => $v) {
                $koujo_row_key = $koujo_row_num + $count;
                $this->spreadsheet->getActiveSheet()->setCellValue('T' . $koujo_row_key, $v["osaka_other_subsidy_name"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('X' . $koujo_row_key, $v["osaka_other_subsidy"] . '円');

                $count++;
            }
            // 控除額合計
            $this->spreadsheet->getActiveSheet()->setCellValue('X32', $job["total_deductions"] . '円');
            // 備考欄
            //セル内改行指定
            $this->spreadsheet->getActiveSheet()->getStyle('T31:T36')->getAlignment()->setWrapText(true);
            $this->spreadsheet->getActiveSheet()->setCellValue('T34', "【備考欄】\n" . $job["notes"]);
            // 差合計
            $minute = ($job["diff_total"] * 10) % 10 === 0 ? ':00' : ':30';
            $this->spreadsheet->getActiveSheet()->setCellValue('AA38', floor($job["diff_total"]) . $minute);
        }



        // Excelファイルをダウンロード
        $file_name = '出勤簿_' . $user->name_last . '_' . $user->name_first . $job->year . '年' . $job->month . '月' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$file_name}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }

    /**
     * Excelファイルを出力.
     *
     * @return void
     */
    public function exportFare($fare_id): void
    {
        $fare = Fare::find($fare_id);
        $user = Fare::find($fare_id)->user;
        $fare_date = isset($fare) ? json_decode($fare->data, true) : null;
        //$tokyo_cell_first['H', '', '', '', '', '', '', '', '', ''];
        //dd($fare_date);



        if ($user->department_id === 2) { // 東京スタッフの場合
            $file = Storage::path(TEMPLATE_FARE_TOKYO);
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

            // ユーザー情報記載
            $this->spreadsheet->getActiveSheet()->setCellValue('C3', $user->name_last . ' ' . $user->name_first);
            $this->spreadsheet->getActiveSheet()->setCellValue('H3', Carbon::parse($fare_date[0]["date"])->format('n/d'));
            $this->spreadsheet->getActiveSheet()->setCellValue('J3', Carbon::parse(end($fare_date)["date"])->format('n/d'));
            // 前半合計記載
            $this->spreadsheet->getActiveSheet()->setCellValue('D133', $fare["first_total"]);
            // 後半合計記載
            $this->spreadsheet->getActiveSheet()->setCellValue('J67', $fare["second_total"]);

            $row_num = 7;
            $row_num_second = 7;
            // 交通費の記載
            foreach ($fare_date as $k => $v) {
                $dt = new Carbon($v["date"]);
                //dd(intval($dt->day));

                if (1 <= intval($dt->day) && intval($dt->day) <= FARE_SEPARATER) {
                    // 月前半の場合
                    $this->spreadsheet->getActiveSheet()->setCellValue('K' . $row_num, $v["sub_total"]);
                    for ($i = 0; $i < FARE_INPUT_ROW_NUM_TOKYO; $i++) {
                        $this->spreadsheet->getActiveSheet()->setCellValue('H' . $row_num, FARE_TRAFFIC_LIST[$v["detail"][$i]['traffic']]);
                        $this->spreadsheet->getActiveSheet()->setCellValue('I' . $row_num, $v["detail"][$i]['route']);
                        $this->spreadsheet->getActiveSheet()->setCellValue('J' . $row_num, $v["detail"][$i]['fare']);

                        $row_num++;
                    }

                    //$row_num += 6;
                } else {
                    $this->spreadsheet->getActiveSheet()->setCellValue('E' . $row_num_second, $v["sub_total"]);
                    for ($i = 0; $i < FARE_INPUT_ROW_NUM_TOKYO; $i++) {
                        $this->spreadsheet->getActiveSheet()->setCellValue('B' . $row_num_second, FARE_TRAFFIC_LIST[$v["detail"][$i]['traffic']]);
                        $this->spreadsheet->getActiveSheet()->setCellValue('C' . $row_num_second, $v["detail"][$i]['route']);
                        $this->spreadsheet->getActiveSheet()->setCellValue('D' . $row_num_second, $v["detail"][$i]['fare']);

                        $row_num_second++;
                    }
                }
            }
        } else { // 大阪スタッフの場合
            $file = Storage::path(TEMPLATE_FARE_OSAKA);
            $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);
            // ユーザー情報記載
            $this->spreadsheet->getActiveSheet()->setCellValue('B3', $user->name_last . ' ' . $user->name_first);

            $this->spreadsheet->getActiveSheet()->setCellValue('F37', $fare["osaka_total"]);

            $row_num = 6;
            foreach ($fare_date as $k => $v) {
                $this->spreadsheet->getActiveSheet()->setCellValue('A' . $row_num, $v["date"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('B' . $row_num, FARE_TRAFFIC_LIST[$v["traffic"]]);
                $this->spreadsheet->getActiveSheet()->setCellValue('C' . $row_num, $v["start"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('D' . $row_num, $v["end"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('E' . $row_num, FARE_WAY_LIST[$v["way"]]);
                $this->spreadsheet->getActiveSheet()->setCellValue('F' . $row_num, $v["fare"]);
                $this->spreadsheet->getActiveSheet()->setCellValue('G' . $row_num, $v["route"]);

                $row_num++;
            }


            // $file = Storage::path(TEMPLATE_KINTAI_OSAKA);
            // $this->spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file);

            // // ユーザー情報記載
            // $this->spreadsheet->getActiveSheet()->setCellValue('D4', $user->name_last . ' ' . $user->name_first);
            // $this->spreadsheet->getActiveSheet()->setCellValue('S4', Carbon::parse($job_date[0]["date"])->format('n/d'));
            // $this->spreadsheet->getActiveSheet()->setCellValue('Y4', Carbon::parse(end($job_date)["date"])->format('n/d'));

            // // 勤怠の記載
            // $row_num = EXPORT_DATE_START_ROW;
            // //$count = 0;
            // foreach ($job_date as $k => $v) {
            //     $this->spreadsheet->getActiveSheet()->setCellValue('A' . $row_num, Carbon::parse($v['date'])->format('j') . '日');
            //     $this->spreadsheet->getActiveSheet()->setCellValue('D' . $row_num, Carbon::parse($v['date'])->isoFormat('ddd'));
            //     $this->spreadsheet->getActiveSheet()->setCellValue('F' . $row_num, $v['start_time'] !== EMPTY_DATE ? $v['start_time'] : '');
            //     $this->spreadsheet->getActiveSheet()->setCellValue('I' . $row_num, $v['end_time'] !== EMPTY_DATE ? $v['end_time'] : '');
            //     $this->spreadsheet->getActiveSheet()->setCellValue('L' . $row_num, $v['place']);

            //     $row_num += 1;
            // }

            // // 出勤日数
            // $this->spreadsheet->getActiveSheet()->setCellValue('X7', $job["working_days"] . '日');
            // // 時間外労働
            // $this->spreadsheet->getActiveSheet()->setCellValue('X8', $job["overtime_work"] . '時間');
            // // 基本給
            // $this->spreadsheet->getActiveSheet()->setCellValue('X10', $job["base_salary"] . '円');
            // // みなし残業代
            // $this->spreadsheet->getActiveSheet()->setCellValue('X11', $job["osaka_deemed_overtime_pay"] . '円');
            // // 通勤手当
            // $this->spreadsheet->getActiveSheet()->setCellValue('X12', $job["osaka_traffic_expenses"]  . '円');
            // // 特別手当
            // $this->spreadsheet->getActiveSheet()->setCellValue('X14', $job["osaka_special_allowance"] . '円');
            // // その他手当
            // $this->spreadsheet->getActiveSheet()->setCellValue('X15', $job["osaka_other_allowances"] . '円');
            // // 総支給額
            // $this->spreadsheet->getActiveSheet()->setCellValue('X20', $job["all_payment"] . '円');

            // // 健康保険
            // $this->spreadsheet->getActiveSheet()->setCellValue('X22', $job["health_insurance"] . '円');
            // // 厚生年金
            // $this->spreadsheet->getActiveSheet()->setCellValue('X23', $job["welfare_pension"] . '円');
            // // 雇用保険
            // $this->spreadsheet->getActiveSheet()->setCellValue('X24', $job["unemployment_insurance"] . '円');
            // // 源泉所得税
            // $this->spreadsheet->getActiveSheet()->setCellValue('X26', $job["income_tax"] . '円');
            // // 市民税
            // $this->spreadsheet->getActiveSheet()->setCellValue('X27', $job["municipal_tax"] . '円');
            // // 控除額合計
            // $this->spreadsheet->getActiveSheet()->setCellValue('X29', $job["total_deductions"] . '円');
            // // 備考欄
            // //セル内改行指定
            // $this->spreadsheet->getActiveSheet()->getStyle('T31:T36')->getAlignment()->setWrapText(true);
            // $this->spreadsheet->getActiveSheet()->setCellValue('T31', "【備考欄】\n" . $job["notes"]);
            // // 差合計
            // $minute = ($job["diff_total"] * 10) % 10 === 0 ? ':00' : ':30';
            // $this->spreadsheet->getActiveSheet()->setCellValue('AA38', floor($job["diff_total"]) . $minute);
        }



        // Excelファイルをダウンロード
        $file_name = '交通費明細_' . $user->name_last . '_' . $user->name_first . $fare->year . '年' . $fare->month . '月' . '.xlsx';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet;');
        header("Content-Disposition: attachment; filename=\"{$file_name}\"");
        header('Cache-Control: max-age=0');

        $writer = IOFactory::createWriter($this->spreadsheet, 'Xlsx');
        $writer->save('php://output');
        exit;
    }
}
