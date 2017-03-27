<?php
namespace app\index\controller;

class Read extends Base
{
    private static $model = null, $bookId = 0;

    public function _initialize()
    {
        Base::_initialize();
        self::$model = model('index/index');

        // $this->apiStatus();
        self::$bookId = isset(self::$route['id']) ? self::$route['id'] * 1 : 0;
    }

    public function index()
    {
        $type = self::$route['tpye'];
        $this->assign('book_id', self::$bookId);
        $this->assign('type', $type);

        switch ($type) {
            case 'TXT':
                return $this->fetch('index/readtxt');
                break;
            case 'EPUB':
                return $this->fetch('index/readepub');
                break;
        }
    }

    public function getContent()
    {
        $type = self::$route['tpye'];
        $r = self::$model->getBookDatas(array('book_id' => self::$bookId,
            'format' => $type));

        $info = self::$model->bookInfo(array('book_id' => self::$bookId));
        $filePath = self::$baseConfig['Calibre-database'] . $info[0]['path'] . '/' . $r[0]['data_name'] . '.' . strtolower($type);
        $fileContent = '';

        $cacheName = sha1($filePath);
        $cacheValue = cache($cacheName);
        $Etag = '';
        if (empty($cacheValue)) {
            if (file_exists($filePath))
                $fileContent = file_get_contents($filePath);
            $Etag = md5($fileContent);
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
        header('Content-type: text/plain');
        exit($fileContent);
    }

    public function getEpubContent()
    {
        return;
    }
}
