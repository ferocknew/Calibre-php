<?php
namespace app\index\controller;

class Rated extends Base
{
    private static $model = null;

    public function _initialize()
    {
        Base::_initialize();
        self::$model = model('index/index');

        // $this->apiStatus();
    }

    public function index()
    {
        $this->assign('title', lang('Best rated Books'));
        $r = self::$model->getBookList();

        return $this->fetch('index/index');
    }
}
