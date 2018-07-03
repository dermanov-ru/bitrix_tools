<?php
/*
 * Created by PhpStorm.
 * Date: 26.09.2017
 *
 * @author m.dermanov@artw.ru
 */


namespace Tools;


use Bitrix\Main\Application;

class Logger
{
    const LOGS_ROOT = "/_logs/project/";
    
    /**
     * Делает запись в лог.
     * Если запущено из консоли - выводит сразу на консоль.
     * Если задан ГЕТ параметр и запущено через браузер - выведет в браузер.
     * 
     * Функция удобно использовать для пошаговых скриптов, где важно
     * отслеживать именно прогресс, а не состояние переменных. 
     * И где вся полезная информация помещается на одной строке.
     * */
    public static function logProgress($str, $type){
        $dir = self::LOGS_ROOT . $type . "/";
        CheckDirPath($dir);
    
        $log_file = $dir . date("Y_m_d") . ".txt";
    
    
        if ($_REQUEST["LOG"] == "Y")
            echo $str . "<br>";
    
        if (php_sapi_name() == 'cli')
            self::consoleWriteLn( $str );
    
        $str = "\n\n" . date("d-m-Y H:i:s") . "\n" . $str;
        file_put_contents($log_file, $str, FILE_APPEND);
    }
    
    /**
     * Вывод строку на экран во время исполнения консольного скрипта.
     * */
    public static function consoleWriteLn($msg) {
        if (php_sapi_name() == 'cli')
            fputs(STDOUT, $msg . "\n");
        else {
            ob_implicit_flush(true);
            ob_end_flush();
            
            echo $msg . "<br>";
        }
    }
    
    public static function log( $title, $info, $type = "any" )
    {
        $requestUri = Application::getInstance()->getContext()->getRequest()->getRequestUri();

        if (!is_array($info)) {
            $infoTmp = $info;

            $info = array();
            $info["info"] = $infoTmp;
        }

        $info["request_url"] = $requestUri;
        $info["datetime"] = date("d.m.Y H:i:s");
    
    
        $type = str_replace("\\", "/", $type);
        $logDir = self::LOGS_ROOT . $type . "/";
        $logAbsDir = $_SERVER["DOCUMENT_ROOT"] . $logDir;
        $logName =  date("Y_m_d") . ".txt";
        $logFile =  $logDir . $logName;

        if (!CheckDirPath($logAbsDir) ) {
            if (Debug::showExceptionToUser())
                throw new \Exception ( "cant create log dir: $logAbsDir" );
        }

        $title = "TITLE => " . $title;

        if (is_readable($logAbsDir)) {
            \Bitrix\Main\Diag\Debug::writeToFile(var_export($info, true), $title, $logFile);
            \Bitrix\Main\Diag\Debug::writeToFile("--------------------------------------------------------------------------------------", "", $logFile);
        }
    }

    public static function debug( $title, $info, $type )
    {
        $type = "debug/$type/";

        if (Debug::writeDebugLogs()) {
            self::log($title, $info, $type);
        }
    }

    public static function warning( $title, $info, $type )
    {
        $type = "warning/$type/";

        self::log($title, $info, $type);

        if (Debug::showExceptionToUser()) {
            $titleDetailes = $title . " -> DETAILES";
            $titleDetailes = "WARNING " . $titleDetailes . " WARNING";
            echo "<pre><=== $titleDetailes ? ===></pre><pre>" . print_r($info, 1) . "</pre><pre><\=== $titleDetailes ===></pre>";
            throw new \Exception ( $title );
        }
    }

    public static function critical( $title, $info, $type, $exception = false )
    {
        $type = "critical/$type/";

        self::log($title, $info, $type);

        if (Debug::showExceptionToUser() || $exception) {
            $titleDetailes = $title . " -> DETAILES";
            $titleDetailes = "CRITICAL " . $titleDetailes . " CRITICAL";
            echo "<pre><=== $titleDetailes ? ===></pre><pre>" . print_r($info, 1) . "</pre><pre><\=== $titleDetailes ===></pre>";
            throw new \Exception ( $title );
        }

        // TODO send email
    }
}