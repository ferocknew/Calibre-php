<?php
namespace app\index\controller;

class Cover extends Base
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
        if (empty(self::$get['p']))
            return;

        $imgPath = self::$baseConfig['Calibre-database'] . self::$get['p'] . '/cover.jpg';
        $cacheName = sha1($imgPath);
        $cacheValue = cache($cacheName);
        $Etag = '';
        if (empty($cacheValue)) {
            $imgContent = file_get_contents($imgPath);
            $Etag = md5($imgContent);
            cache($cacheName, $Etag, 3600);
        } else {
            $Etag = $cacheValue;
        }

        if (isset($_SERVER['HTTP_IF_NONE_MATCH'])) {
            if ($_SERVER['HTTP_IF_NONE_MATCH'] == $Etag) {
                header("HTTP/1.1 304 Not Modified");
                exit();
            }

        }

        header('Cache-Control:max-age=2592000');
        header("Etag:" . $Etag);
        header('Content-type: image/jpeg');
        echo $imgContent;
        exit;
    }
}
