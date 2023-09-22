<?php
namespace app\traits\orm\boot;

use app\system\database\drivers\dm\orm\DMConnections;
use app\system\database\drivers\postgre\orm\PostgresConnection;
use app\traits\ThemeDatabaseName;
use Illuminate\Container\Container;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Connection;
use Illuminate\Events\Dispatcher;

trait Manager
{
    /**
     * @var string[] 驱动映射
     */
    private static $driverMap = [
        'dm' => 'dm',
        'postgre' => 'pgsql',
        'pdo' => 'mysql',
        'mysqli' => 'mysql',
    ];

    /**
     * @var bool
     */
    private static $bootedManager = false;

    /**
     * 启动manager
     */
    final static function bootManager()
    {
        // 只需要启动一次即可
        if (self::$bootedManager) {
            return;
        }

        self::$bootedManager = true;
        $capsule = new Capsule(app());
        $capsule->setEventDispatcher(new Dispatcher(new Container()));

        // Make this Capsule instance available globally via static methods... (optional)
        $capsule->setAsGlobal();

        // Setup the Eloquent ORM... (optional; unless you've used setEventDispatcher())
        $capsule->bootEloquent();

        require APPPATH . 'config/' . ENVIRONMENT . '/database.php';
        /** @var array $db */
        foreach ($db as $connection => $config) {
            $capsule->addConnection(
                static::connectionConfig($connection, $config),
                $connection
            );
        }
        app()->instance('db', $capsule->getDatabaseManager());
        app()->instance('capsule', $capsule);
        //添加达梦驱动
        Connection::resolverFor('dm', function ($connection, $database, $prefix, $config) {
            return new DMConnections($connection, $database, $prefix, $config);
        });
        //添加自定义的pg驱动
        Connection::resolverFor('pgsql', function ($connection, $database, $prefix, $config) {
            return new PostgresConnection($connection, $database, $prefix, $config);
        });
    }

    /**
     * demo环境根据projectName参数获取对应的数据库名
     * @param string $connection
     * @param string $defaultDatabase
     * @return string
     */
    private static function getDatabaseName(string $connection, string $defaultDatabase): string
    {
        if ('demo' !== ENVIRONMENT || 'project' !== $connection) {
            return $defaultDatabase;
        }
        return ThemeDatabaseName::getDatabaseName();
    }

    /**
     * 数据库连接配置
     *
     * @param string $connection 连接名称
     * @param array $config 配置
     * @return array
     */
    public static function connectionConfig(string $connection, array $config): array
    {
        $result = [
            'driver' => self::$driverMap[$config['dbdriver']],
            'host' => $config['hostname'],
            'port' => $config['port'],
            'database' => self::getDatabaseName($connection, $config['database']),
            'username' => $config['username'],
            'password' => $config['password'],
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ];
        //添加模式配置
        if ($config['schema']) {
            $result['schema'] = $config['schema'];
        }
        //pdo特殊配置
        if ($config['options']) {
            $result['options'] = $config['options'];
        }

        return $result;
    }
}
