<?php

namespace HongXunPan\Tools\Db;

interface DBInterface
{
    public static function setConfig(array $config = [], $connectName = 'default', array $options = []);

    public static function getConnection($connectName = 'default');

    public static function connection($connectName = 'default');
}
