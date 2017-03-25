<?php
namespace app\index\controller;

class Index extends Base
{
    private static $model = null;

    public function _initialize()
    {
        Base::_initialize();
        self::$model = model('index/Index');

        // $this->apiStatus();
    }

    public function index()
    {
        //        $r = self::$model->index();
        //        halt($r);
        $this->assign('title', lang('New Books'));
        return $this->fetch('index');
    }
}
