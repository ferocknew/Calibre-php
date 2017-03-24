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

    /**
     * @return mixed
     */
    public function index()
    {
        $r = self::$mydb->query('select path from books limit 1');

        $path = config('calibre-database') . $r[0]['path'] . '/';
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
