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
     * ����������� ����
     *
     * @param $date string ���� (�������, ������ ���������� ��������)
     *
     * @param $format string ������ ���� (� ������ Bitrix API)
     *
     * @return string ������ 05.11.2015 15:37:00 => 5 ������ 2015
     */
    public static function formatDate ($date, $format = "j F Y") {
        $utf = defined("BX_UTF");
        return mb_strtolower( \CIBlockFormatProperties::DateFormat($format, strtotime($date)), $utf ? "UTF-8" : "CP-1251" );
    }
}