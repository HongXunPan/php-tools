<?php

require dirname(__DIR__) . '/check.php';

$envPath = isset($argv[1]) ? $argv[1] : '';

$json = [
    'envPath' => $envPath,
];

\HongXunPan\Tools\Env\Env::cliConfig(false, $json);