<?php

/**
 * @author HongXunPan <me@kangxuanpeng.com>
 * @date 2023-06-30 17:47
 */
function check()
{
    if (file_exists(dirname(__DIR__) . '/vendor/autoload.php')) {
        require dirname(__DIR__) . '/vendor/autoload.php';
    } else {
        if (file_exists(dirname(__DIR__, 4) . '/vendor/autoload.php')) {
            require dirname(__DIR__, 4) . '/vendor/autoload.php';
        } else {
            echo 'can not load autoload.php';
            exit();
        }
    }

    if (posix_getpwuid(posix_geteuid())['name'] == 'root') {
        echo "\e[32m \e[1m
    please do not run this script using root
\e[0m" . PHP_EOL;
        exit();
    }
}

check();
