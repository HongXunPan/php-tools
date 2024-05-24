<?php

namespace HongXunPan\Tools\ElasticSearch;

use Elastic\Elasticsearch\ClientBuilder;
use HongXunPan\DB\DBContract;

class ElasticSearch extends DBContract
{
    protected $connectionClass = ElasticSearchConnection::class;

    protected function ping($connection)
    {
        return $connection;
    }

    protected function connect(array $config)
    {
        return ClientBuilder::create()           // 实例化 ClientBuilder
        ->setHosts($config)      // 设置主机信息
        ->build();
    }

    public static function setConfig(array $config = [], $connectName = 'default', array $options = [])
    {
        parent::saveConfig($config, $connectName);
    }
}