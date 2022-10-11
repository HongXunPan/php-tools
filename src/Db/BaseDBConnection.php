<?php

namespace HongXunPan\Tools\Db;

abstract class BaseDBConnection
{
    private $connection;

    public function __construct($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @deprecated 仅用于代码提示
     */
    public function getConnection()
    {
        return $this->connection;
    }

    abstract protected function recordLog($name, $arguments);

    public function __call($name, $arguments)
    {
        $exist = method_exists($this->connection, $name);
        if (!$exist) {
            throw new DBException("method '$name' does not exist");
        }
        $result = $this->connection->$name(...$arguments);
        $this->recordLog($name, $arguments);
        return $result;
    }
}