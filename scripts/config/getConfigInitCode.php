<?php

require dirname(__DIR__) . '/check.php';

$config = \HongXunPan\Tools\Config\Config::cliConfig();
$canCache = json_encode((bool)$config['canCache']);
$configPath = $config['configPath'];
$cachePath = $config['cachePath'];

echo "please add this code to your index or init file:
\e[32m \e[1m
    \HongXunPan\Tools\Config\Config::getInstance()->loadConfig({$canCache}, '$configPath', '$cachePath');
\e[0m" .PHP_EOL;

echo "and add this code to your helper or init file:
\e[32m \e[1m
    if (!function_exists('config')) {
        function config(\$key)
        {
            return HongXunPan\Tools\Config\Config::getInstance()->getConfig(\$key);
        }
    }\e[0m
";