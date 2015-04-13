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

    /**
     * @param $locale
     * @return array
     */
    public static function translateEmail($locale)
    {
        return require_once ROOT . '/app/data/email_'. $locale .'.php';
    }


    /**
     * @param $locale
     * @param $page
     * @return array
     */
    public static function pageTranslates($locale, $page)
    {
        return require_once ROOT . '/app/data/pages/'. $page .'_'. $locale .'.php';
    }

}