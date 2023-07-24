## env\env (\HongXunPan\Tools\Env)

## script

`sudo -uwww /usr/local/php8.0/bin/php /usr/local/bin/composer run-script hxpSetEnvPath path`

## use

```php
    if (!function_exists('env')) {
        /**
         * @param $key
         * @param $default
         * @return bool|array|string|null
         * @throws Exception
         * @author HongXunPan <me@kangxuanpeng.com>
         */
        function env($key, $default = null): bool|array|string|null
        {
            return Env::get($key, $default);
        }
    }
```