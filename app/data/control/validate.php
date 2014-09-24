<?php
namespace app\Action;

Class ValidateAction
{
    /**
     * @param array $data
     * @return bool
     */
    public static function validateEmail($data = array())
    {
        $noError = true;
        foreach ($data as $value) {
            $value = trim($value);
            if (empty($value)) {
                $noError = false;
            }
        }

        return $noError;
    }

    public static function getPlanName($plan)
    {

    }
}