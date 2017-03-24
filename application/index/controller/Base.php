<?php
namespace app\index\controller;

use think\Controller;
use think\Session;
use think\Config;

// 引入类库
// use think\auth\Auth;

class Base extends Controller
{
    private static $bcLength = 0;
    public static $post = null, $get = null, $param = null, $route = null, $auth = null;
    public static $session = null;

    public function _initialize()
    {
        Config::parse(ROOT_PATH . 'Db/config.json', 'json');
        //        $moduleName = request()->module();
        //        Session::prefix($moduleName);

        self::$post = request()->post();
        self::$get = request()->get();
        // self::$param = request()->param();
        self::$route = request()->route();
        // self::$session = session($moduleName);

        self::$bcLength = config('base.bcLength');
        bcscale(self::$bcLength);
    }

    /**
     * 判断用户登录状态
     */
    public function apiStatus()
    {
        return true;
    }


}
