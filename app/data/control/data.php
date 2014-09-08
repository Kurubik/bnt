<?php
namespace app\data\Data;

Class Translates
{
    /**
     * @param $locale
     * @return array
     */
    public static function translateArray($locale)
    {
        return require_once ROOT . '/app/data/data_'. $locale .'.php';
    }

}