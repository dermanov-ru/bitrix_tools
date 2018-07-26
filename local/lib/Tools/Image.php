<?php
/**
 * Created by PhpStorm.
 * Date: 13.12.2017
 * Time: 1:42
 *
 * @author dev@dermanov.ru
 */


namespace Tools;


class Image
{
    /**
     * Масштабирует фото, сохраняет копию файла и возвращает путь к нему
     * либо возвращает ссылку на картинку-заглушку
     *
     * ---
     *
     * Водяной знак - если существует файл /upload/watermark/watermark_original.png - он будет
     * смасштабирован под фото и нанесен на всю поверхность с небольшим отступом от края.
     * watermark_original.png - должен быть большого размера, чтобы не терялось качество.
     *
     * @param $imgId
     * @param $width int
     * @param $height int Если не задано, будет равно ширине
     *
     * @throws Exception File dimensions can not be a null
     *
     *
     * @return string Путь к измененному файлу
     *
     * @see https://dermanov.ru/exp/bitrix-resize-image-and-watermark/ - Примеры работы функции
     */
    public static function getResizedImgOrPlaceholder( $imgId, $width, $height = 0, $proportional = false){
        if (!$width)
            throw new \Exception( "File dimensions can not be a null" );
        
        $resizeType = BX_RESIZE_IMAGE_EXACT;
        $autoHeightMax = 9999;
        
        //
        if ($height == "auto") {
            $height = $autoHeightMax;
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
        }
        if (!$height) $height = $width;
        
        if ($proportional)
            $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL;
        
        // if img is null - returns dummy img
        //you can insert here custom img
        //or even save on disk on first call each dummy img
        if (!$imgId) {
            $customNoImg = SITE_TEMPLATE_PATH . "/images/img_placeholder.jpg";
            
            // dummy can't be very big
            $height = $height == $autoHeightMax ? $width : $height;
            
            return file_exists($_SERVER["DOCUMENT_ROOT"] . $customNoImg) ? $customNoImg : "http://dummyimage.com/{$width}x{$height}/5C7BA4/fff";
        }
        
        $arFilters = Array();
        
        /*
         * <watermark>
         * 1) получаем размер ($arDestinationSize) итоговой картинки (фото товара) после ресайза, с учетом типа ресайза ($resizeType)
         * 2) создаем водяной знак под этот размер фото (он должен быть чуть меньше самого фото)
         * 3) формируем фильтр для наложения знака
         * */
        $watermark = $_SERVER['DOCUMENT_ROOT'] . "/upload/watermark/watermark_original.png";
        
        if (is_readable($watermark)) {
            $bNeedCreatePicture = $arSourceSize = $arDestinationSize = false;
            $imgSize = \CFile::GetImageSize( $_SERVER["DOCUMENT_ROOT"] .  \CFile::GetPath($imgId) );
            \CFile::ScaleImage($imgSize["0"], $imgSize["1"], array("width" => $width, "height" => $height), $resizeType, $bNeedCreatePicture, $arSourceSize, $arDestinationSize);
            
            $koef = 0.95;
            $watermarkResized = $_SERVER['DOCUMENT_ROOT'] . "/upload/watermark/watermark_" . $arDestinationSize["width"] * $koef . ".png";
            
            if (!is_readable($watermarkResized))
                \CFile::ResizeImageFile($watermark, $watermarkResized, array("width" => $arDestinationSize["width"] * $koef, "height" => $arDestinationSize["height"] * $koef), BX_RESIZE_IMAGE_PROPORTIONAL, false, 95, array());
            
            if (is_readable($watermarkResized))
                $arFilters[] = Array(
                    "name"     => "watermark",
                    "position" => "center",
                    "size"     => "real",
                    "file"     => $watermarkResized
                );
        }
        /*
         * </watermark>
         * */
        
        $resizedImg = \CFile::ResizeImageGet($imgId, array("width" => $width, "height" => $height), $resizeType, false, $arFilters, false, 100);
        
        /* если файл по каким-то причинам не создался */
        if (!file_exists($_SERVER["DOCUMENT_ROOT"] . $resizedImg['src'])) {
            // for good looks - make same width and height for "auto" mode.
            if ($height == $autoHeightMax)
                $height = $width;
            
            return self::getResizedImgOrPlaceholder(false, $width, $height, $proportional);
        }
        
        return $resizedImg['src'];
    }
    
}