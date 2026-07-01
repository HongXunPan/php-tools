<?php

namespace HongXunPan\Tools\Lock;

class RedisLockAttemptResult
{
    private $count;

    public function __construct($count)
    {
        $this->count = (int)$count;
    }

    public function count()
    {
        return $this->count;
    }

    public function isAcquired()
    {
        return $this->count === 1;
    }

    public function isRejected()
    {
        return !$this->isAcquired();
    }
}
