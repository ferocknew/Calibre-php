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

    public static function init()
    {
        self::$bcLength = config('base.bcLength');
        bcscale(self::$bcLength);

        self::$mydb = Db::connect([// 数据库类型
            'type' => 'sqlite',
            // 数据库连接DSN配置
            'dsn' => 'sqlite:' . config('calibre-database') . 'metadata.db',
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
            'prefix' => '']);


    }

}
