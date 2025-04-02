<?php

namespace App\Traits;

use Illuminate\Support\Facades\Log;

trait LoggableTrait
{
    /**
     * クラス名に対応したログファイルにをログを出力
     *
     * @param string $message ログメッセージ
     */
    protected function logCurrentMethod($message = '')
    {
        $className = get_class($this);
        $classBaseName = class_basename($this);
        $methodName = debug_backtrace()[1]['function'];

        Log::build([
            'driver' => 'single',
            'path' => storage_path('logs/' . $classBaseName . '_' . date('Y-m-d') . '.log'),
        ])->info("[{$className}::{$methodName}] {$message}");
    }
}
