<?php

namespace HongXunPan\Tools\Log;

final class JsonLineFormatter
{
    public function format($level, $channel, $message, array $context, array $sharedContext = [])
    {
        $record = [
            'timestamp' => $this->timestamp(),
            'level' => strtoupper((string)$level),
            'channel' => (string)$channel,
            'message' => (string)$message,
        ];

        foreach ($sharedContext as $key => $value) {
            if (!array_key_exists($key, $record)) {
                $record[$key] = $value;
            }
        }
        $record['context'] = $context;

        $options = JSON_UNESCAPED_UNICODE
            | JSON_UNESCAPED_SLASHES
            | JSON_PARTIAL_OUTPUT_ON_ERROR;
        $json = json_encode($record, $options);
        $encodingError = json_last_error();
        if ($json !== false && $encodingError !== JSON_ERROR_NONE) {
            $record['encoding_error'] = json_last_error_msg();
            $json = json_encode($record, $options);
        }

        if ($json === false) {
            $json = json_encode([
                'timestamp' => $record['timestamp'],
                'level' => $record['level'],
                'channel' => $record['channel'],
                'message' => '日志 JSON 编码失败',
                'encoding_error' => json_last_error_msg(),
                'context' => [],
            ], $options);
        }

        return $json . PHP_EOL;
    }

    private function timestamp()
    {
        return (new \DateTimeImmutable())->format('Y-m-d\TH:i:s.uP');
    }
}
