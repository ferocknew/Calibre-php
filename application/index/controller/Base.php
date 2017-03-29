<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Config;
use think\Lang;

// 引入类库
// use think\auth\Auth;

class Base extends Controller
{
    private static $bcLength = 0;
    protected static $post = null, $get = null, $param = null, $route = null;
    protected static $auth = null;
    public static $session = null;
    protected static $baseConfig = null;
    protected static $cache = null;

    public function _initialize()
    {

        Lang::setAllowLangList(['zh-CN']);
        Config::parse(ROOT_PATH . 'Db/config.json', 'json', 'baseConfig');

        //        $moduleName = request()->module();
        //        Session::prefix($moduleName);

        self::$baseConfig = config()['baseConfig'];

        self::$post = request()->post();
        self::$get = request()->get();
        self::$param = request()->param();
        self::$route = request()->route();

        self::$bcLength = config('base.bcLength');
        bcscale(self::$bcLength);

        $this->assign('book_data', array());
        $this->assign('instance', self::$baseConfig['title']);
        $this->assign('user', array('nickname' => '用户昵称'));
    }

    /**
     * 判断用户登录状态
     */
    public function apiStatus()
    {
        return true;
    }


    protected function getBookInfo($param = array())
    {
        $bookData = $param['book'];

        foreach ($bookData as $k => $v) {
            $bookData[$k]['path'] = urlencode($v['path']);

            if ($v['identifiers_count'] > 0) {
                foreach ($v['identifiers'] as $k1 => $v1) {
                    switch ($v1['type']) {
                        case 'isbn':
                            $bookData[$k]['identifiers'][$k]['url'] = 'http://www.worldcat.org/isbn/' . $v1['val'];
                            break;
                        default:
                            $bookData[$k]['identifiers'][$k]['url'] = '';
                            break;
                    }

                }
            }
        }

        return $bookData;
    }


}
