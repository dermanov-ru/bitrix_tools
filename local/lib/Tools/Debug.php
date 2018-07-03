<?php
/*
 * Created by PhpStorm.
 * Date: 19.09.2017
 * 
 * @author m.dermanov@artw.ru
 */


namespace Tools;

class Debug
{
    /**
     * get from .settings
     * */
    public static function showExceptionToUser( )
    {
        $exceptionHandling = \Bitrix\Main\Config\Configuration::getValue("project_debug");
        $debugMode = $exceptionHandling["user_exception"];
        
        return $debugMode;
    }
    
    /**
     * get from .settings
     * */
    public static function writeDebugLogs( )
    {
        $exceptionHandling = \Bitrix\Main\Config\Configuration::getValue("project_debug");
        $debugMode = $exceptionHandling["debug_logs"];
        
        return $debugMode;
    }
}