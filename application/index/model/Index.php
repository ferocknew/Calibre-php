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

    public function bookInfo($param = array())
    {
        $r = array();
        $sql = "
        select
          b.`id`,b.`title`,b.`path`,b.`has_cover`,r.`rating`,
          b.`series_index`,bsl.`series`,s.`name` as `series_name`
          ,p.`name` as `publishers`,cm.`text` as `comments`
        from `books` as b
        left join `books_authors_link` as bal on b.`id`=bal.`author`
        left join `authors` as a on a.`id`=bal.`author`
        left join `books_ratings_link` as brl on b.`id`=brl.`book`
        left join `ratings` as r on r.id=brl.`rating`
        left join `books_series_link` as bsl on b.`id`=bsl.`book`
        left join `series` as s on s.`id`=bsl.`series`
        left join `books_publishers_link` as bpl on b.`id`=bpl.`book`
        left join `publishers` as p on p.`id`=bpl.`publisher`
        left join `comments` as cm on b.`id`=cm.`book`
        where b.`id`=?
        ";

        $r = self::$mydb->query($sql, array($param['book_id']));
        $r = $this->expandBookInfo($r);

        if (is_null($r[0]['series'])) {
            $r[0]['series'] = 0;
            $r[0]['series_name'] = '';
        }


        return $r;
    }

    private function expandBookInfo($r)
    {
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


            $i = 1;
            $r[$k]['languages'] = [];
            $r[$k]['languages_count'] = 0;
            $languages = $this->getBookLanguages(array('book_id' => $v['id']));
            foreach ($languages as $v1) {
                $r[$k]['languages'][] = $v1;
                $r[$k]['languages_count'] = $i;
                $i++;
            }

            $i = 1;
            $r[$k]['tags'] = [];
            $r[$k]['tags_count'] = 0;
            $tags = $this->getBookTags(array('book_id' => $v['id']));
            foreach ($tags as $v1) {
                $r[$k]['tags'][] = $v1;
                $r[$k]['tags_count'] = $i;
                $i++;
            }

            $i = 1;
            $r[$k]['identifiers'] = [];
            $r[$k]['identifiers_count'] = 0;
            $identifiers = $this->getBookIdentifiers(array('book_id' => $v['id']));
            foreach ($identifiers as $v1) {
                $r[$k]['identifiers'][] = $v1;
                $r[$k]['identifiers_count'] = $i;
                $i++;
            }

            $i = 1;
            $r[$k]['datas'] = [];
            $r[$k]['datas_count'] = 0;
            $datas = $this->getBookDatas(array('book_id' => $v['id']));
            foreach ($datas as $v1) {
                $r[$k]['datas'][] = $v1;
                $r[$k]['datas_count'] = $i;
                $i++;
            }

        }

        return $r;
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

    public function getBookDatas($param = array())
    {
        $r = array();
        $sqlWhere = '';
        $sqlData = array('book' => $param['book_id']);
        $param['book_id'] = isset($param['book_id']) ? $param['book_id'] : 0;
        if (empty($param['book_id']))
            return false;

        if (isset($param['format'])) {
            $sqlWhere .= ' and format=:format';
            $sqlData['format'] = $param['format'];
        }

        $sql = "
                select a.`id`,a.`name` as `data_name`,`format`
                  from `data` as a
                where a.`book` = :book $sqlWhere
        ";

        $r = self::$mydb->query($sql, $sqlData);

        return $r;
    }

    public function getBookLanguages($param = array())
    {
        $r = array();
        $param['book_id'] = isset($param['book_id']) ? $param['book_id'] : 0;
        if (empty($param['book_id']))
            return false;

        $sql = "
                select a.`id`,a.`lang_code`
                  from `languages` as a
                  left join `books_languages_link` as bll on a.`id`=bll.`lang_code`
                where bll.`book` = :book
                order by bll.`lang_code` ASC 
        ";

        $r = self::$mydb->query($sql, array('book' => $param['book_id']));

        return $r;
    }

    public function getBookTags($param = array())
    {
        $r = array();
        $param['book_id'] = isset($param['book_id']) ? $param['book_id'] : 0;
        if (empty($param['book_id']))
            return false;

        $sql = "
                select a.`id`,a.`name` as `tag_name`
                  from `tags` as a
                  left join `books_tags_link` as btl on a.`id`=btl.`tag`
                where btl.`book` = :book
                order by btl.`tag` ASC 
        ";

        $r = self::$mydb->query($sql, array('book' => $param['book_id']));

        return $r;
    }

    public function getBookIdentifiers($param = array())
    {
        $r = array();
        $param['book_id'] = isset($param['book_id']) ? $param['book_id'] : 0;
        if (empty($param['book_id']))
            return false;

        $sql = "
                select a.`id`,a.`type`,a.`val`
                  from `identifiers` as a
                where a.`book` = :book
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

                $r = self::$mydb->table('books')->alias('b')->field('b.`id`,`title`,r.`rating`,b.`has_cover`,b.`path`')->join('`books_ratings_link` brl', 'b.`id`=brl.`book`')->join('`ratings` r', 'r.id=brl.`rating`')->where('r.`rating` > 9')->order('b.`id` desc')->paginate(self::$baseConfig['pageSize'], true);
                trace(self::$mydb->getLastSql(), 'SQL');
                break;
            default:
                $r = self::$mydb->table('books')->alias('b')->field('b.`id`,`title`,r.`rating`,b.`has_cover`,b.`path`')->join('`books_ratings_link` brl', 'b.`id`=brl.`book`')->join('`ratings` r', 'r.id=brl.`rating`')->where('')->order('b.`id` desc')->paginate(self::$baseConfig['pageSize'], true);
                trace(self::$mydb->getLastSql(), 'SQL');
                break;
        }

        $data = $r->items();
        $r['data'] = $this->expandBookInfo($data);

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
