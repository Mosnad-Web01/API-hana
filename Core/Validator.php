<?php

namespace Core;

use Respect\Validation\Validator as v;

class Validator
{
    private $errors = [];

    public static function string($value, $min = 1, $max = INF)
    {
        $value = trim($value);
        return strlen($value) >= $min && strlen($value) <= $max;
    }

    public static function email($value)
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL);
    }

    public static function title($data)
    {
        $errors = [];
        if (!v::stringType()->notEmpty()->length(5, 255)->validate($data->title ?? null)) {
            $errors['title'] = 'Title is required and must be between 5 and 255 characters.';

        }
        return $errors;
    }

    public static function description($data)
    {
        $errors = [];
        if (!v::stringType()->notEmpty()->length(1, 1000)->validate($data->description ?? null)) {
            $errors['description'] = 'Description is required and must be between 1 and 1000 characters.';
        }
        return $errors;
    }

    public static function user_id($data)
    {
        $errors = [];
        if (!v::number()->notEmpty()->validate($data->user_id ?? null)) {
            $errors['user_id'] = 'User ID is required and must be a valid number.';
        }
        return $errors;
    }

    public static function validate(array $fields, $data)
    {
        $errors = []; // مصفوفة لتخزين الأخطاء

        foreach ($fields as $field) {
            $methodName = strtolower($field); // توليد اسم الدالة الديناميكي
            if (method_exists(__CLASS__, $methodName)) {
                $fieldErrors = call_user_func([__CLASS__, $methodName], $data);
                if (!empty($fieldErrors)) {
                    $errors[$field] = $fieldErrors; // إضافة الأخطاء إلى المصفوفة
                }
            } else {
                $errors[$field] = ["error" => "Validation method for $field does not exist."];
            }
        }

        return $errors; // إرجاع الأخطاء
    }
}
