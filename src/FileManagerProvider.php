<?php

namespace WalkerSpider\FileManager;

class FileManagerProvider {
    public static function CsvToArray($file, String $delimiter) {
        return new CsvToArray($file, $delimiter);
    }
}