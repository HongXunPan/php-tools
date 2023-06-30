<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

if (posix_getpwuid(posix_geteuid())['name'] == 'root') {
    echo "\e[32m \e[1m
    please do not run this script using root
\e[0m" . PHP_EOL;
    exit();
}

$envPath = isset($argv[1]) ? $argv[1] : '';

$json = [
    'envPath' => $envPath,
];

\HongXunPan\Tools\Env\Env::cliConfig(false, $json);