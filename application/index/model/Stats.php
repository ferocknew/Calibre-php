<?php
namespace app\index\model;

use think\File;

/**
 * Class Index
 * @package app\index\model
 */
class Stats extends Base
{
    public static function init()
    {
        Base::init();
    }


    /**
     * @return mixed
     */
    public function authorCount()
    {
        $r = array();
        $r = self::$mydb->query('select count(*) as num from `authors`');

        return $r[0]['num'];
    }

    /**
     * @return mixed
     */
    public function bookCount()
    {
        $r = self::$mydb->query('select count(*) as num from books');

        return $r[0]['num'];
    }

    /**
     * @return mixed
     */
    public function tagsCount()
    {
        $r = self::$mydb->query('select count(*) as num from `tags`');

        return $r[0]['num'];
    }

    /**
     * @return mixed
     */
    public function seriesCount()
    {
        $r = self::$mydb->query('select count(*) as num from `series`');

        return $r[0]['num'];
    }


}
