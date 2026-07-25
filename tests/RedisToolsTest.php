<?php

use HongXunPan\Tools\Cache\Cache;
use HongXunPan\Tools\Draw\RedisDraw;
use HongXunPan\Tools\Lock\RedisLock;
use HongXunPan\Tools\TimeLimitOffers\RedisTimeLimitOffers;
use HongXunPan\Tools\TimeLimitOffers\TimeLimitOffersException;

function testCacheRepairs()
{
    $redis = new FakeRedisClient();
    Cache::getInstance()->setConfig('test:', $redis);
    $calls = 0;
    $value = Cache::remember('key', 30, function () use (&$calls) {
        $calls++;

        return 'computed';
    });
    $cached = Cache::remember('key', 30, function () use (&$calls) {
        $calls++;

        return 'changed';
    });

    assertSameValue('computed', $value, '首次 remember 没有返回计算结果');
    assertSameValue('computed', $cached, '第二次 remember 没有返回缓存结果');
    assertSameValue(1, $calls, '缓存命中后仍执行了回调');
    assertSameValue(30, $redis->expires['test:key'], '缓存 TTL 没有生效');
}

function testRedisLockRepairs()
{
    $redis = new FakeRedisClient();
    $lock = new RedisLock('user-1', null, $redis);
    assertTrueValue($lock->attemptUserLock(10)->isAcquired(), '首次加锁必须成功');
    assertTrueValue($lock->attemptUserLock(10)->isRejected(), '重复加锁必须被拒绝');
    assertTrueValue(isset($redis->values['lock:user-1']), '默认锁名称没有生效');
    $lock->clearUserLock();
    assertTrueValue(!isset($redis->values['lock:user-1']), '释放锁失败');

    $managed = RedisLock::withUserLock(
        ['userId' => 'user-2', 'redis' => $redis, 'time' => 10],
        function () {
            return 'done';
        }
    );
    assertSameValue('done', $managed, '托管锁没有返回业务结果');
    assertTrueValue(!isset($redis->values['lock:user-2']), '托管锁没有在业务结束后释放');

    assertThrows(
        'InvalidArgumentException',
        function () use ($redis) {
            (new RedisLock('user-3', 'lock', $redis))->attemptUserLock(0);
        },
        '非正数锁 TTL 必须失败'
    );
}

function testRedisDraw()
{
    $redis = new FakeRedisClient();
    $draw = new RedisDraw('round', $redis);
    assertSameValue(3, $draw->addUser2Pool([1, 2, 3]), '奖池添加人数错误');
    assertSameValue(3, $draw->getPoolUserCount(), '奖池人数错误');
    assertSameValue(2, count($draw->draw(2, false)), '抽奖人数错误');
    assertSameValue(1, $draw->getPoolUserCount(), '不重复抽奖没有移除中奖成员');

    assertThrows(
        'Exception',
        function () use ($draw) {
            $draw->draw(0);
        },
        '非正数抽奖人数必须失败'
    );
}

function testTimeLimitOfferAtomicClaim()
{
    $redis = new FakeRedisClient();
    $first = new RedisTimeLimitOffers('user-1', 1, 'round', $redis);
    assertTrueValue($first->isHaveChance(), '首个用户没有获得名额');
    assertTrueValue($first->isHaveChance(), '同一用户重复请求应保持幂等');

    $second = new RedisTimeLimitOffers('user-2', 1, 'round', $redis);
    $exception = assertThrows(
        'HongXunPan\\Tools\\TimeLimitOffers\\TimeLimitOffersException',
        function () use ($second) {
            $second->isHaveChance();
        },
        '名额已满时必须失败'
    );
    assertSameValue(TimeLimitOffersException::NO_CHANCE_LEFT, $exception->getCode(), '名额已满异常码错误');
    assertSameValue(0, $second->getChanceLeft(), '剩余名额错误');
    assertSameValue(['user-1'], $first->getHadChanceUsers(), '已获得名额用户错误');
}

return [
    'Cache 修复' => 'testCacheRepairs',
    'RedisLock 修复' => 'testRedisLockRepairs',
    'Redis 抽奖' => 'testRedisDraw',
    '抢购名额原子领取' => 'testTimeLimitOfferAtomicClaim',
];
