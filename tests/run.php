<?php

require __DIR__ . '/bootstrap.php';

$tests = array_merge(
    require __DIR__ . '/CompatibilityTest.php',
    require __DIR__ . '/CoreToolsTest.php',
    require __DIR__ . '/RedisToolsTest.php'
);

foreach ($tests as $name => $test) {
    call_user_func($test);
    echo '[通过] ' . $name . PHP_EOL;
}

echo '全部回归测试通过。' . PHP_EOL;
