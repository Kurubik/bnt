<?php
namespace app\Action;

Class ValidateAction
{
    /**
     * @param array $data
     * @return bool
     */
    public static function validate($data = array())
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
}