<?php

namespace core;

class AssetHelper
{
    /**
     * Return file version based on creation date like app.js?v=20240318101854
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
