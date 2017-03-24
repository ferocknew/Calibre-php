<?php
namespace app\index\controller;

class Search extends Base
{
    private static $model = null;

    public function _initialize()
    {
        Base::_initialize();
        // self::$model = model('index/Search');

        // $this->apiStatus();
    }

    public function index()
    {
        //        $r = self::$model->index();
        //        halt($r);

        return 'Search';
    }

    public function advanced()
    {
        return 'advanced';
    }
}
