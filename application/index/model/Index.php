<?php
namespace app\index\model;

use think\File;

/**
 * Class Index
 * @package app\index\model
 */
class Index extends Base
{
    public static function init()
    {
        Base::init();
    }

    public function getBookList()
    {
        $r = array();

        $r = self::$mydb->query('
        select b.`id`,`title`,r.`rating`
          from `books` as b
          left join `books_ratings_link` as brl on b.`id`=brl.`book`
          left join `ratings` as r on r.id=brl.`rating`
        where r.`rating` > 9
        order by b.`timestamp` desc
        limit :limit;
        ', array('limit' => self::$baseConfig['pageSize']));
        halt($r);

        return $r;
    }

    /**
     * @return mixed
     */
    public function index()
    {
        $r = self::$mydb->query('select path from books limit 1');

        $path = self::$basePath . $r[0]['path'] . '/';
        $dir = $path;//这里输入其它路径
        //PHP遍历文件夹下所有文件
        $handle = opendir($dir . ".");
        //定义用于存储文件名的数组
        $array_file = array();
        while (false !== ($file = readdir($handle))) {
            if ($file != "." && $file != "..") {
                $array_file[] = $file; //输出文件名
            }
        }
        closedir($handle);
        dump($array_file);

    }

}
