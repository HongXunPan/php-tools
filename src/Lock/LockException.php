<?php

namespace HongXunPan\Tools\Lock;

use Exception;

class LockException extends Exception
{
    public static function forRejected($userId, $lockName, $count)
    {
        return new self('Lock Fail: ' . $userId . ' -> ' . $lockName . ' [count=' . (int)$count . ']');
    }
}
