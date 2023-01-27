<?php


namespace DBUtils;

class FileDB
{
    public static function initializeDB(string $filename): void
    {
        if (file_exists($filename)) {
//            unlink($filename);
        }
    }
}