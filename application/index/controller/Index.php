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
        $r = self::$model->getBookList();
        $this->assign('book_data', $r['data']);

        $page = $r->render();

        $this->assign('page', $page);
        $this->assign('title', lang('New Books'));

        return $this->fetch('index');
    }

    public function hot()
    {
        $r = self::$model->getBookList(array('type' => 'hot'));
        $this->assign('book_data', $r['data']);

        $page = $r->render();

        $this->assign('page', $page);
        $this->assign('title', lang('Hot Books'));

        return $this->fetch('index');
    }

    public function discover()
    {
        $r = self::$model->getBookList(array('type' => 'discover'));
        $this->assign('book_data', $r['data']);

        $page = $r->render();

        $this->assign('page', $page);
        $this->assign('title', lang('Discover'));

        return $this->fetch('index');
    }
}
