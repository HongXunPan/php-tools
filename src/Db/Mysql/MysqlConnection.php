<?php

namespace HongXunPan\Tools\Db\Mysql;

use HongXunPan\Tools\Db\BaseDBConnection;
use PDO;

class MysqlConnection extends BaseDBConnection
{
    /**
     * @param $connection
     * @param string $connectName
     * @return MysqlConnection
     */
    public function __construct($connection, $connectName = 'default')
    {
        return parent::__construct($connection, $connectName);
    }

    /**
     * @return PDO
     * @deprecated
     * @author HongXunPan <me@kangxuanpeng.com>
     * @date 2022-10-11 17:05
     * @noinspection PhpDeprecationInspection
     */
    public function getConnection()
    {
        return parent::getConnection();
    }

    protected function recordLog($name, $arguments)
    {
    }
}
