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
    public static function getOrderID()
    {
        $order_no = date('ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(4), 7, 13), 1))), 0, 8);

        return $order_no;
    }

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
