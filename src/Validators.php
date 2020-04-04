<?php

namespace MEkramy\Support;

use Hekmatinasser\Verta\Verta;

class Validators
{

    public function username($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^[0-9a-zA-Z\-\._]+$/", $value);
    }
    public function usernameReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.username");
    }

    public function tel($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^(\(0\d{2}\) \d{4}-\d{4})|(0\d{10})$/", $value);
    }
    public function telReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.tel");
    }

    public function mobile($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^(\(09\d{2}\) \d{3}-\d{4})|(09\d{9})$/", $value);
    }
    public function mobileReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.mobile");
    }

    public function postalcode($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^(\d{5}-\d{5})|(\d{10})$/", $value);
    }
    public function postalcodeReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.postalcode");
    }

    public function identifier($attribute, $value, $parameters, $validator)
    {
        return is_numeric($value) && intval($value) > 0;
    }
    public function identifierReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.identifier");
    }

    public function minlength($attribute, $value, $parameters, $validator)
    {
        $length = (isset($parameters[0]) && is_numeric($parameters[0])) ? intval($parameters[0]) : 1;
        return mb_strlen($value) >= $length;
    }
    public function minlengthReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.minlength");
    }

    public function maxlength($attribute, $value, $parameters, $validator)
    {
        $length = (isset($parameters[0]) && is_numeric($parameters[0])) ? intval($parameters[0]) : 1;
        return mb_strlen($value) <= $length;
    }
    public function maxlengthReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.maxlength");
    }

    public function unsigned($attribute, $value, $parameters, $validator)
    {
        return is_numeric($value) && intval($value) >= 0;
    }
    public function unsignedReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.unsigned");
    }

    public function range($attribute, $value, $parameters, $validator)
    {
        if (is_numeric($value)) {
            $min = is_numeric($parameters[0]) ? floatval($parameters[0]) : 0;
            $max = is_numeric($parameters[1]) ? floatval($parameters[1]) : 1;
            $val = floatval($value);
            if ($val < $min || $val > $max) {
                return false;
            } else {
                return true;
            }
        }
        return false;
    }
    public function rangeReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.between");
    }

    public function idnumber($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^\d{1, 10}$/", $value);
    }
    public function idnumberReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.idnumber");
    }

    public function nationalcode($attribute, $value, $parameters, $validator)
    {
        return preg_match("/^(\d{3}-\d{6}-\d{1})|(\d{10})$/", $value);
    }
    public function nationalcodeReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.nationalcode");
    }

    public function jalali($attribute, $value, $parameters, $validator)
    {
        if (!is_string($value)) {
            return false;
        }
        $format = count($parameters) ? $parameters[0] : 'Y/m/d';
        try {
            Verta::parseFormat($format, $value);
        } catch (\Exception $e) {
            return false;
        }
        return true;
    }
    public function jalaliReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.date");
    }
    public function numericarray($attribute, $value, $parameters, $validator)
    {
        if (!is_array($value) || count($value) == 0) {
            return false;
        }
        foreach ($value as $v) {
            if (!is_int($v)) return false;
        }
        return true;
    }
    public function numericarrayReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.numericarray");
    }

    public function length($attribute, $value, $parameters, $validator)
    {
        $length = (isset($parameters[0]) && is_numeric($parameters[0])) ? intval($parameters[0]) : 1;
        return mb_strlen($value) <= $length;
    }
    public function lengthReplacer($message, $attribute, $rule, $parameters)
    {
        return trans("mekramy-support::validations.length");
    }
}
