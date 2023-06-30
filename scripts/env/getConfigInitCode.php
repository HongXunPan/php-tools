<?php

require dirname(dirname(__DIR__)) . '/vendor/autoload.php';

echo "please add this code to your helper or init file:
\e[32m \e[1m
    if (!function_exists('env')) {
        function env(\$key, \$default = null)
        {
            return Env::get(\$key, \$default);
        }
    }\e[0m
";