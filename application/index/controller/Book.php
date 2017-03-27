<?php
namespace app\index\controller;

class Book extends Base
{
    private static $model = null;
    private static $bookId = 0;

    public function _initialize()
    {
        Base::_initialize();
        self::$model = model('index/index');

        self::$bookId = isset(self::$route['id']) ? self::$route['id'] * 1 : 0;
    }

    public function index()
    {

        $this->assign('title', lang('Best rated Books'));
        $r = self::getBookInfo(array('book' => self::$model->bookInfo(array('book_id' => self::$bookId))));
        trace($r[0]);
        $this->assign('bookInfo', $r[0]);

        return $this->fetch('index/detail');
    }
}
