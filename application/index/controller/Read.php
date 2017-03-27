<?php
namespace app\index\controller;

use s\fn;

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
                $this->unzipEpub(array('book_id' => self::$bookId));

                $this->assign('book_id', self::$bookId);
                return $this->fetch('index/readepub');
                break;
        }
    }

    public function download()
    {
        $type = self::$route['tpye'];
        $filePath = $this->getFilePath();

        $r = self::$model->getBookDatas(array('book_id' => self::$bookId,
            'format' => $type));
        $fileName = $r[0]['data_name'] . '.' . strtolower($type);

        switch ($type) {
            case 'TXT':
                header("Content-Type:text/plain");
                break;
            case 'EPUB':
                header("Content-Type:application/epub");
                break;
        }

        //处理中文文件名
        $ua = $_SERVER["HTTP_USER_AGENT"];
        $encoded_filename = rawurlencode($fileName);
        if (preg_match("/MSIE/", $ua)) {
            header('Content-Disposition: attachment; filename="' . $encoded_filename . '"');
        } else {
            header('Content-Disposition: attachment; filename="' . $fileName . '"');
        }


        header('Content-Length:' . filesize($filePath));
        readfile($filePath);
    }

    public function getContent()
    {
        $filePath = $this->getFilePath();
        $fileContent = '';

        $cacheName = sha1($filePath);
        $cacheValue = cache($cacheName);
        $Etag = '';
        if (empty($cacheValue)) {
            if (file_exists($filePath))
                // $fileContent = file_get_contents($filePath);
                $fileContent=readfile($filePath);
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
        readfile($filePath);
    }

    private function unzipEpub($param = array())
    {
        $filePath = $this->getFilePath();

        $cacheName = sha1($filePath);
        $cacheValue = cache($cacheName);
        if (empty($cacheValue)) {
            if (file_exists($filePath)) {
                $fileMd5 = md5_file($filePath);
            }
            $dirPath = ROOT_PATH . 'public' . self::$baseConfig['epubPath'] . $param['book_id'] . '/';
            if (file_exists($dirPath))
                fn::delDir($dirPath);
            fn::mkDirs($dirPath);

            $zip = new \ZipArchive;//新建一个ZipArchive的对象
            /*
            通过ZipArchive的对象处理zip文件
            $zip->open这个方法的参数表示处理的zip文件名。
            如果对zip文件对象操作成功，$zip->open这个方法会返回TRUE
            */
            if ($zip->open($filePath) === true) {
                $zip->extractTo($dirPath);//假设解压缩到在当前路径下images文件夹的子文件夹php
                $zip->close();//关闭处理的zip文件
            }

            cache($cacheName, $fileMd5, 3600);
        }

        return;
    }

    private function getFilePath()
    {
        $type = self::$route['tpye'];
        $r = self::$model->getBookDatas(array('book_id' => self::$bookId,
            'format' => $type));

        $info = self::$model->bookInfo(array('book_id' => self::$bookId));
        $filePath = self::$baseConfig['Calibre-database'] . $info[0]['path'] . '/' . $r[0]['data_name'] . '.' . strtolower($type);

        return $filePath;
    }
}
