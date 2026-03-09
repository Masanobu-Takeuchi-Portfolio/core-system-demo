<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class PuppeteerService
{
    /**
     * Node.jsスクリプトのパス
     */
    private string $scriptPath;

    public function __construct()
    {
        $this->scriptPath = base_path('node_scripts/scraper.js');
    }

    /**
     * 指定URLのスクレイピングを実行する
     *
     * @param string $url スクレイピング対象のURL
     * @param string|null $selector CSSセレクタ（省略時は主要要素を取得）
     * @return array スクレイピング結果
     */
    public function scrape(string $url, ?string $selector = null): array
    {
        // URLのバリデーション
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return [
                'success' => false,
                'error' => '無効なURLです。正しいURLを入力してください。',
            ];
        }

        // コマンドの組み立て
        $command = sprintf(
            'node %s %s',
            escapeshellarg($this->scriptPath),
            escapeshellarg($url)
        );

        if ($selector) {
            $command .= ' ' . escapeshellarg($selector);
        }

        // stderr用
        $command .= ' 2>&1';

        // コマンドの実行
        $output = [];
        $returnCode = 0;
        exec($command, $output, $returnCode);

        $outputStr = implode("\n", $output);

        // 結果のパース
        $result = json_decode($outputStr, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('Puppeteerスクレイピングエラー: JSONパース失敗', [
                'output' => $outputStr,
                'return_code' => $returnCode,
            ]);

            return [
                'success' => false,
                'error' => 'スクレイピング結果の解析に失敗しました。',
            ];
        }

        if ($returnCode !== 0 || (isset($result['success']) && $result['success'] === false)) {
            Log::error('Puppeteerスクレイピングエラー', [
                'error' => $result['error'] ?? '不明なエラー',
                'url' => $url,
            ]);

            return [
                'success' => false,
                'error' => $result['error'] ?? 'スクレイピング中にエラーが発生しました。',
            ];
        }

        return $result;
    }
}
