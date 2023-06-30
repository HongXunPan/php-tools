<?php

require dirname(__DIR__) . '/check.php';

$configPath = isset($argv[1]) ? $argv[1] : '';
$cachePath = isset($argv[2]) ? $argv[2] : '';
$canCache = isset($argv[3]) ? (bool)$argv[3] : true;

$json = [
    'configPath' => $configPath,
    'cachePath' => $cachePath,
    'canCache' => $canCache,
];

\HongXunPan\Tools\Config\Config::cliConfig(false, $json);