<?php

require dirname(__DIR__) . '/vendor/autoload.php';

error_reporting(E_ALL);

set_error_handler(function ($severity, $message, $file, $line) {
    if (!(error_reporting() & $severity)) {
        return false;
    }

    throw new ErrorException($message, 0, $severity, $file, $line);
});

function assertSameValue($expected, $actual, $message)
{
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message . '，期望：' . var_export($expected, true) . '，实际：' . var_export($actual, true)
        );
    }
}

function assertTrueValue($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

function assertThrows($exceptionClass, callable $callback, $message)
{
    try {
        call_user_func($callback);
    } catch (Exception $exception) {
        if ($exception instanceof $exceptionClass) {
            return $exception;
        }

        throw new RuntimeException($message . '，实际异常：' . get_class($exception));
    }

    throw new RuntimeException($message . '，实际未抛出异常');
}

function makeTestDirectory($name)
{
    $path = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'php-tools-' . $name . '-' . uniqid('', true);
    if (!mkdir($path, 0777, true) && !is_dir($path)) {
        throw new RuntimeException('无法创建测试目录：' . $path);
    }

    return $path;
}

function removeTestDirectory($path)
{
    if (!is_dir($path)) {
        return;
    }

    $items = scandir($path);
    foreach ($items as $item) {
        if ($item === '.' || $item === '..') {
            continue;
        }
        $itemPath = $path . DIRECTORY_SEPARATOR . $item;
        if (is_dir($itemPath) && !is_link($itemPath)) {
            removeTestDirectory($itemPath);
        } else {
            unlink($itemPath);
        }
    }
    rmdir($path);
}

final class FakeRedisClient
{
    public $values = [];
    public $hashes = [];
    public $sets = [];
    public $expires = [];

    public function get($key)
    {
        return array_key_exists($key, $this->values) ? $this->values[$key] : false;
    }

    public function set($key, $value)
    {
        $this->values[$key] = $value;

        return true;
    }

    public function setex($key, $ttl, $value)
    {
        $this->values[$key] = $value;
        $this->expires[$key] = (int)$ttl;

        return true;
    }

    public function del($key)
    {
        $exists = array_key_exists($key, $this->values);
        unset($this->values[$key], $this->expires[$key]);

        return $exists ? 1 : 0;
    }

    public function __call($name, $arguments)
    {
        if ($name !== 'eval' || count($arguments) !== 3) {
            throw new BadMethodCallException('不支持的 Redis 测试方法：' . $name);
        }

        return $this->evaluateScript($arguments[0], $arguments[1], $arguments[2]);
    }

    private function evaluateScript($script, array $arguments, $numberOfKeys)
    {
        if (strpos($script, 'HEXISTS') !== false) {
            return $this->claim($arguments, $numberOfKeys);
        }

        $key = $arguments[0];
        $ttl = (int)$arguments[1];
        $count = isset($this->values[$key]) ? (int)$this->values[$key] + 1 : 1;
        $this->values[$key] = $count;
        if ($count === 1 && $ttl > 0) {
            $this->expires[$key] = $ttl;
        }

        return $count;
    }

    private function claim(array $arguments, $numberOfKeys)
    {
        assertSameValue(2, $numberOfKeys, '抢购名额 Lua 的 key 数量错误');
        $countKey = $arguments[0];
        $usersKey = $arguments[1];
        $userId = (string)$arguments[2];
        $limits = (int)$arguments[3];
        $time = $arguments[4];

        if (isset($this->hashes[$usersKey][$userId])) {
            return [1, (int)$this->get($countKey)];
        }

        $count = $this->get($countKey);
        $count = $count === false ? 0 : (int)$count;
        if ($count >= $limits) {
            return [0, $count];
        }

        $count++;
        $this->values[$countKey] = $count;
        if (!isset($this->hashes[$usersKey])) {
            $this->hashes[$usersKey] = [];
        }
        $this->hashes[$usersKey][$userId] = $time;

        return [1, $count];
    }

    public function hGetAll($key)
    {
        return isset($this->hashes[$key]) ? $this->hashes[$key] : [];
    }

    public function expire($key, $ttl)
    {
        $this->expires[$key] = (int)$ttl;

        return true;
    }

    public function expireAt($key, $timestamp)
    {
        $this->expires[$key] = (int)$timestamp;

        return true;
    }

    public function sAddArray($key, array $members)
    {
        if (!isset($this->sets[$key])) {
            $this->sets[$key] = [];
        }
        $added = 0;
        foreach ($members as $member) {
            $memberKey = (string)$member;
            if (!isset($this->sets[$key][$memberKey])) {
                $added++;
            }
            $this->sets[$key][$memberKey] = $member;
        }

        return $added;
    }

    public function sRem($key)
    {
        $arguments = func_get_args();
        array_shift($arguments);
        $removed = 0;
        foreach ($arguments as $member) {
            $memberKey = (string)$member;
            if (isset($this->sets[$key][$memberKey])) {
                unset($this->sets[$key][$memberKey]);
                $removed++;
            }
        }

        return $removed;
    }

    public function sIsMember($key, $member)
    {
        return isset($this->sets[$key][(string)$member]);
    }

    public function sCard($key)
    {
        return isset($this->sets[$key]) ? count($this->sets[$key]) : 0;
    }

    public function sMembers($key)
    {
        return isset($this->sets[$key]) ? array_values($this->sets[$key]) : [];
    }

    public function sRandMember($key, $count)
    {
        return array_slice($this->sMembers($key), 0, (int)$count);
    }

    public function sPop($key, $count)
    {
        $members = array_slice($this->sMembers($key), 0, (int)$count);
        if ($members) {
            call_user_func_array([$this, 'sRem'], array_merge([$key], $members));
        }

        return $members;
    }

    public function unlink($key)
    {
        $exists = isset($this->sets[$key]);
        unset($this->sets[$key]);

        return $exists ? 1 : 0;
    }
}
