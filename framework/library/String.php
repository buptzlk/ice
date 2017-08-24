<?php

/**
 * Created by PhpStorm.
 * Author by a super man
 * Email <17888835130@163.com>
 * Date: 17/8/24
 * Time: 14:25
 */
class Framework_Library_String {

    /**
     * 将下划线命名转换为驼峰式命名
     * @param string $str 形式为aa_bb_cc的字串
     * @param bool|false $ucFirst 首字母是否大写，默认小写
     * @return string
     */
    public static function underLineToCamel($str, $ucFirst = false)
    {
        while (($pos = strpos($str, '_')) !== false) {
            $str = substr($str, 0, $pos) . ucfirst(substr($str, $pos + 1));
        }
        return $ucFirst ? ucfirst($str) : $str;
    }
}