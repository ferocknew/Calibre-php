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

    public function getBookAuthors($param = array())
    {
        $r = array();
        $param['book_id'] = isset($param['book_id']) ? $param['book_id'] : 0;
        if (empty($param['book_id']))
            return false;

        $sql = "
                select a.`id`,a.`name` as `author`
                  from `authors` as a
                  left join `books_authors_link` as bal on a.`id`=bal.`author`
                where bal.`book` = :book
                order by a.`id` desc
        ";

        $r = self::$mydb->query($sql, array('book' => $param['book_id']));
        return $r;
    }

    public function getBookList($param = array())
    {
        $r = array();
        $param['type'] = isset($param['type']) ? $param['type'] : 'index';
        $sql = '';
        switch ($param['type']) {
            case 'Rated':
                $sql = '
                        select b.`id`,`title`,r.`rating`,b.`has_cover`,b.`path`
                          from `books` as b
                          left join `books_ratings_link` as brl on b.`id`=brl.`book`
                          left join `ratings` as r on r.id=brl.`rating`
                        where r.`rating` > 9
                        order by b.`timestamp` desc
                        limit :limit;
                        ';
                break;
            default:
                break;
        }


        $r = self::$mydb->query($sql, array('limit' => self::$baseConfig['pageSize']));

        foreach ($r as $k => $v) {
            $authors = $this->getBookAuthors(array('book_id' => $v['id']));
            $i = 1;
            foreach ($authors as $v1) {
                $r[$k]['author'][] = $v1;
                $r[$k]['author_count'] = $i;
                $i++;
            }
            $r[$k]['rating'] = floor($v['rating'] / 2);
            // $r[$k]['rating'] = 4;
            $r[$k]['rating_diff'] = 5 - $r[$k]['rating'];
        }
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
