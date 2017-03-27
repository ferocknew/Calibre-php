<?php
/**
 * Created by PhpStorm.
 * User: yingjun
 * Date: 2016/11/29
 * Time: 上午12:08
 */
namespace s;

class fn
{
    /**
     * 处理prepare的 where in 需要的数组
     * 返回的是字符串
     * @author  jonah.fu
     * @param   array $arr
     * @return  array
     */
    public static function array4prepare_in($arr)
    {
        $defaultKeyPrefix = 'in_';
        $return = array();

        array_map(function ($v) use (&$return, $defaultKeyPrefix) {
            $key = $defaultKeyPrefix . $v;
            $return[$key] = $v;
        }, $arr);

        return $return;
    }

    public static function delDir($dir)
    {
        //先删除目录下的文件：
        $dh = opendir($dir);
        while ($file = readdir($dh)) {
            if ($file != "." && $file != "..") {
                $fullpath = $dir . "/" . $file;
                if (!is_dir($fullpath)) {
                    unlink($fullpath);
                } else {
                    self::deldir($fullpath);
                }
            }
        }

        closedir($dh);
        //删除当前文件夹：
        if (rmdir($dir)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getOrderID()
    {
        $order_no = date('ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(4), 7, 13), 1))), 0, 8);

        return $order_no;
    }

    /**
     * 检查目标文件夹是否存在，如果不存在则自动创建该目录
     *
     * @access      public
     * @param       string      folder     目录路径。不能使用相对于网站根目录的URL
     *
     * @return      bool
     */
    public static function mkDirs($folder)
    {
        $reval = false;

        if (!file_exists($folder)) {
            /* 如果目录不存在则尝试创建该目录 */
            @umask(0);

            /* 将目录路径拆分成数组 */
            preg_match_all('/([^\/]*)\/?/i', $folder, $atmp);

            /* 如果第一个字符为/则当作物理路径处理 */
            $base = ($atmp[0][0] == '/') ? '/' : '';

            /* 遍历包含路径信息的数组 */
            foreach ($atmp[1] AS $val) {
                if ('' != $val) {
                    $base .= $val;

                    if ('..' == $val || '.' == $val) {
                        /* 如果目录为.或者..则直接补/继续下一个循环 */
                        $base .= '/';

                        continue;
                    }
                } else {
                    continue;
                }

                $base .= '/';

                if (!@file_exists($base)) {
                    /* 尝试创建目录，如果创建失败则继续循环 */
                    if (@mkdir(rtrim($base, '/'), 0755)) {
                        @chmod($base, 0755);
                        $reval = true;
                    }
                }
            }
        } else {
            /* 路径已经存在。返回该路径是不是一个目录 */
            $reval = is_dir($folder);
        }

        clearstatcache();

        return $reval;
    }


    /**
     * 处理prepare的字符串
     */
    public static function str4prepare($arr)
    {
        $return = '';
        $i = 0;
        foreach ($arr as $k => $v) {
            $return .= $k . '=:' . $k;
            if ($i < count($arr) - 1)
                $return .= ",";
            ++$i;
        }

        return $return;
    }


    /**
     * 处理prepare的字符串
     * @param   array $arr
     * @return  string
     */
    public static function str4insert_prepare($arr)
    {
        $return = '';
        $i = 0;
        foreach ($arr as $k => $v) {
            $return .= ':' . $k;
            if ($i < count($arr) - 1)
                $return .= ",";
            ++$i;
        }

        return $return;
    }

}
