<?php
/**
 * Created by PhpStorm.
 * Date: 02.05.2018
 * Time: 1:08
 *
 * @author dev@dermanov.ru
 */


namespace Tools;


class Date
{
    /**
     * Форматирует дату
     *
     * @param $date string Дата (наример, начало активности элемента)
     *
     * @param $format string Формат даты (в рамках Bitrix API)
     *
     * @return string Пример 05.11.2015 15:37:00 => 5 Ноября 2015
     */
    public static function formatDate ($date, $format = "j F Y") {
        $utf = defined("BX_UTF");
        return mb_strtolower( \CIBlockFormatProperties::DateFormat($format, strtotime($date)), $utf ? "UTF-8" : "CP-1251" );
    }
}