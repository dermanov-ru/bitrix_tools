<?php
/**
 * Created by PhpStorm.
 * Date: 02.05.2018
 * Time: 23:43
 *
 * @author dev@dermanov.ru
 */


namespace Tools;


class String
{
    /**
     * Returns project encoding for string functions
     * (mb_substr, etc)
     * */
    public static function getEncoding()
    {
        $result = defined("BX_UTF") ? "UTF-8" : "CP-1251";
        
        return $result;
    }
}