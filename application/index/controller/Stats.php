<?php
namespace app\index\controller;

class Stats extends Base
{
    private static $model = null;

    public function _initialize()
    {
        Base::_initialize();
        self::$model = model('index/Stats');

        // $this->apiStatus();
    }

    public function index()
    {
        $this->assign('bookcounter', self::$model->bookCount());
        $this->assign('authorcounter', self::$model->authorCount());
        $this->assign('categorycounter', self::$model->tagsCount());
        $this->assign('seriecounter', self::$model->seriesCount());

        return $this->fetch('index/stats');
    }
}
