<?php

namespace HongXunPan\Tools\Log;

final class FileLogWriter
{
    public function write($filePath, $content, $failureHandler = null, array $failureContext = [])
    {
        $expectedBytes = strlen($content);
        $writtenBytes = @file_put_contents(
            $filePath,
            $content,
            FILE_APPEND | LOCK_EX
        );

        if ($writtenBytes === $expectedBytes) {
            return true;
        }

        $details = array_merge($failureContext, [
            'file' => $filePath,
            'expected_bytes' => $expectedBytes,
            'written_bytes' => $writtenBytes === false ? null : $writtenBytes,
        ]);
        $this->reportFailure($details, $failureHandler);

        return false;
    }

    private function reportFailure(array $details, $failureHandler)
    {
        if (is_callable($failureHandler)) {
            try {
                call_user_func($failureHandler, $details);

                return;
            } catch (\Exception $exception) {
                $details['handler_failure'] = get_class($exception);
            } catch (\Throwable $throwable) {
                $details['handler_failure'] = get_class($throwable);
            }
        }

        error_log(sprintf(
            '[php-tools:log-write-failed] channel=%s level=%s file=%s written=%s expected=%d handler_failure=%s',
            isset($details['channel']) ? $details['channel'] : '',
            isset($details['level']) ? $details['level'] : '',
            isset($details['file']) ? $details['file'] : '',
            isset($details['written_bytes']) && $details['written_bytes'] !== null
                ? $details['written_bytes']
                : 'false',
            isset($details['expected_bytes']) ? $details['expected_bytes'] : 0,
            isset($details['handler_failure']) ? $details['handler_failure'] : ''
        ));
    }
}
