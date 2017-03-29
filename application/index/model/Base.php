<?php
namespace app\index\model;

use think\Model;
use think\Db;
use think\config;

class Base extends Model
{
    private static $bcLength = 0;
    protected static $mydb = null;
    protected static $baseDb = null;
    protected static $basePath = '';
    protected static $baseConfig = null;

    public static function init()
    {
        self::$baseConfig = config()['baseConfig'];
        self::$basePath = self::$baseConfig['Calibre-database'];
        self::$bcLength = config('base.bcLength');
        bcscale(self::$bcLength);

        self::$mydb = Db::connect([// 数据库类型
            'type' => 'sqlite',
            // 数据库连接DSN配置
            'dsn' => 'sqlite:' . self::$basePath . 'metadata.db',
            // 数据库连接参数
            'params' => [\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                // PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES UTF8" ,
                // PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => TRUE,
                // \PDO::ATTR_AUTOCOMMIT => true,
                \PDO::ATTR_EMULATE_PREPARES => false],
            // 数据库编码默认采用utf8
            'charset' => 'utf8',
            // 数据库表前缀
            'prefix' => '',
            'debug' => 1,
            'fields_strict' => 1,
            'resultset_type' => 'array']);

        self::$mydb->listen(function ($sql, $time, $explain) {
            // 记录sql
            trace($sql . ' [' . $time . 's]', 'SQL');
            // 查看性能分析结果
            trace($explain, 'SQL');
        });


    }

}
