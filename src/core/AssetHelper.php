<?php

namespace core;

class AssetHelper
{
    /**
     * @param $filePath
     * @return string
     */
    public static function getVersion($filePath): string
    {
        $path = $_SERVER['DOCUMENT_ROOT'] . '/'. $filePath;

        if (file_exists($path)) {
            return date("YmdHms", filemtime($path));
        }

        return date("YmdHms");
    }
}
