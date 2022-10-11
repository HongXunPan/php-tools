<?php

namespace HongXunPan\Tools\Db\Mysql;

use Exception;
use HongXunPan\Tools\Db\BaseDB;
use PDO;

class Mysql extends BaseDB
{

    public static function setConfig(array $config = [], $connectName = 'default', array $options = [])
    {
        $default = [
            'host' => '127.0.0.1',
            'port' => 3306,
            'dbname' => 'test',
            'username' => '',
            'password' => '',
        ];
        $dbConfig = array_merge($default, $config);
        $dsn = 'mysql:host=' . $dbConfig['host'] . ';port:' . $dbConfig['port'];
        $config = [
            'dsn' => $dsn,
            'username' => $dbConfig['username'],
            'password' => $dbConfig['password'],
            'options' => $options,
        ];

        parent::saveConfig($config, $connectName);
    }

    /**
     * @param PDO $connection
     * @return PDO|false
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-11 16:14
     */
    protected function ping($connection)
    {
        try {
            if ($connection->getAttribute(PDO::ATTR_SERVER_INFO)) {
                return $connection;
            }
        } catch (Exception $e) {
        }
        return false;
    }

    protected function connect(array $config)
    {
        return new PDO($config['dsn'], $config['username'], $config['password'], $config['options']);
    }

    public static function connection($connectName = 'default')
    {
        return new MysqlConnectionBase(self::getConnection($connectName));
    }
}
