<?php


class BaseClass
{
    protected $_docFileName = "Base.doc.txt";

    static function doc()
    {
        if (file_exists(static::$_docFileName))
            return file_get_contents(static::$_docFileName);
        else
            return "ERROR: Doc file by path: './".static::$_docFileName."' is missing".PHP_EOL;
    }
}