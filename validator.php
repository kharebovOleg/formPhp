<?php

class Validator {
    public function check($rules, $data): array
    {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            $errors[$field] = '';
            // Разбиваем строку с правилами
            foreach (explode(';', $ruleSet) as $rule) {
                // Разделяем правило и параметры (например, min:3)
                [$ruleName, $parameter] = explode(':', $rule);
                // Вызываем соответствующие методы для валидации
                switch ($ruleName) {
                    case 'empty':
                        if($this->validateEmpty($data[$field])) {
                            $errors[$field] .= 'обязательное поле не заполнено; ';
                        // другие требования нет смысла проверять, выходим из foreach
                        continue 3;
                        }                        
                       
                    case 'min':
                        if(!$this->validateMin($data[$field], $parameter)) {
                            $errors[$field] .= 'количество символов слишком маленькое; ';
                        }
                        break;
                    case 'max':
                        if(!$this->validateMax($data[$field], $parameter)) {
                            $errors[$field] .= 'количество символов слишком большое; ';
                        }
                        break;
                    case 'email':
                        if(!$this->validateMail($data[$field])) {
                            $errors[$field] .= 'неправильно указанна почта; ';
                        }
                        break;
                    case 'phone':
                        if(!$this->validatePhone($data[$field])) {
                            $errors[$field] .= 'возможно в номере телефона есть буквы; ';
                        }
                        break;
                    case 'date':
                        if(!$this->validateDate($data[$field])) {
                            $errors[$field] .= 'в этом месяце этого года нет такого числа; ';
                        }
                        break;
                }
            }
        }
        return $errors;
    }

    function validateMin($elem, $parameter)
    {
        return strlen($elem) < $parameter ? false : true;
    }
    function validateMax($elem, $parameter)
    {
        return strlen($elem) > $parameter ? false : true;
    }
    function validateMail($mail)
    {
        return filter_var($mail, FILTER_VALIDATE_EMAIL);
    }
    function validateEmpty($elem)
    {
        return $elem === null || trim($elem) === '';
    }
    function validatePhone($number)
    {
        return preg_match('/^[0-9]+$/', $number);
    }
    function validateDate($date) {
        $arr = explode('-', $date);
        return checkdate($arr[1], $arr[0], $arr[2]);
    }
}

/*
    // Массив с правилами валидации
    ['email' => 'min:3;max:30;unik:users']

    // Данные для проверки
    ['email' => 'test@example.com']
    
*/