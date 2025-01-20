<?php

class Validator {
    public function check($rules, $data) {
        $errors = [];
        foreach ($rules as $field => $ruleSet) {
            // Разбиваем строку с правилами
            foreach (explode(';', $ruleSet) as $rule) {
                // Разделяем правило и параметры (например, min:3)
                [$ruleName, $parameter] = explode(':', $rule);
                // Вызываем соответствующие методы для валидации
                switch ($ruleName) {
                    case 'min':
                        if(!$this->validateMin($data[$field], $parameter)) {
                            print_r($data[$field]);
                            $errors[$field] = 'колчество символов слишком маленькое';
                        }
                        break;
                    // case 'max':
                    //     $this->validateMax($data[$field], $parameter);
                    //     break;
                    // case 'email':
                    //     $this->validateMail($data[$field]);
                    //     break;
                    // case 'empty':
                    //     $this->validateEmpty($data[$field]);
                    //     break;
                    // case 'phone':
                    //     $this->validatePhone($data[$field]);
                    //     break;
                    // case 'date':
                    //     $this->validateDate($data[$field]);
                    //     break;
                }
            }
        }
        return $errors;
    }

    function validateMin($elem, $parameter) {
        return strlen($elem) < $parameter ? false : true;
    }
}

/*
    // Массив с правилами валидации
    ['email' => 'min:3;max:30;unik:users']

    // Данные для проверки
    ['email' => 'test@example.com']
    
*/