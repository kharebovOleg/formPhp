<?php
require_once 'validator.php';

$validate = new Validator(); //валидатор принимает правила валидации и объект валидации, возвращает массив с ошибками

$keysAndValues = prepareArrayForValidate($_POST); //массив который будем валидировать

$keys = array_keys($keysAndValues);

$rules = createRules($keys); //правила валидации

$errors = $validate->check($rules, $keysAndValues);

$response = createResponse($errors, $keysAndValues);

$jsonResponse = writeToJson($response);


printArrayInfo('Массив для валидации', $keysAndValues);
printArrayInfo('Правила валидации', $rules);
printArrayInfo('Результаты валидации', $errors);

print_r("\n" . "JSON" . "\n\n");
print_r(prettyPrint($jsonResponse));

if($response['succes'] === true) sendMail($keysAndValues['email'], $jsonResponse);




/** prepareArrayForValidate
 * убирает ключи ['day'], ['month], ['year'] из массива,
 * добавляет ключ ['date'],
 * нормализует телефонные номера
 * убирает незаполненные необязательные поля
 */
function prepareArrayForValidate(array $arr): array
{
    $res = $arr;
    $keys = array_keys($arr);
    if(isAllDateFieldsFilled($keys)) {
        $date = createDate();
        $res['date'] = $date;
    }

    $res['phone-number'] = normalizePhoneNumber($res['phone-number']);

    if(in_array('add-number', $keys)) {
        $res['add-number'] = normalizePhoneNumber($res['add-number']);
    }
    $res = deleteEmptyOptionalFields($res);

    unset($res['day']);
    unset($res['month']);
    unset($res['year']);
    return $res;
}

function deleteEmptyOptionalFields(array $res): array
{
    $res;
    if($res['add-number'] === '') unset($res['add-number']);
    if($res['patronymic'] === '') unset($res['patronymic']);
    return $res;
}

// все ли ключи ['day'], ['month], ['year'] есть
function isAllDateFieldsFilled(array $keys): bool
{
    return in_array('day',$keys) && in_array('month',$keys) && in_array('year',$keys);
}

function createDate()
{
   $day = $_POST['day'];
   $month = normalizeMonth($_POST['month']);
   $year = $_POST['year'];

    $date = "{$day}-{$month}-{$year}";
    return $date;
}

function normalizePhoneNumber($phone)
{
    $tel = trim((string) $phone);
    $changedPlusSeven = str_replace("+7", '8', $tel);
    $justNums = preg_replace("/[^0-9A-zА-я\+]/", '', $changedPlusSeven); 
    
    return $justNums;
}

function normalizeMonth(string $month): string
{
    switch($month) {
        case 'января':
            return '01';
        case 'февраля':
            return '02';
        case 'марта':
            return '03';
        case 'апреля':
            return '04';
        case 'мая':
            return '05';
        case 'июня':
            return '06';
        case 'июля':
            return '07';
        case 'августа':
            return '08';
        case 'сентября':
            return '09';
        case 'октября':
            return '10';
        case 'ноября':
            return '11';
        case 'декабря':
            return '12';
    }
}

function createRules(array $keys): array
{
    $res = [];
    foreach($keys as $key) {
        switch($key) {
            case 'email':
                $res[$key] = 'empty:0;email:0';
                break;
            case 'name':
            case 'sirname':
                $res[$key] = 'empty:0;min:2;max:20';
                break;
            case 'patronymic':
                $res[$key] = 'min:2;max:20';
                break;
            case 'phone-number':
                $res[$key] ='empty:0;max:11;min:11;phone:0';
                break;
            case 'add-number':
                $res[$key] = 'max:11;min:11;phone:0';
                break;
            case 'date':
                $res[$key] = 'date:0';
                break;
        }
    }

    return $res;
}

/**
 * функция, которая посмотрит на массив errors и если не найдет ошибки
 * в обязательных полях, вернет true
 */
function hasAnyErrrorInRequiredFields(array $errors, array $requiredFields): bool
{
    $res = false;
    foreach($errors as $key => $value) {
        if(in_array($key,$requiredFields)) $res = true;
    }
    return $res;
}


//из массива ошибок сделает ответ JSON
function createResponse(array $errors)
{
    $requiredFields = ['email', 'phone-number', 'name', 'sirname'];
    
    $response = [];
    $response['succes'] = false;
    $response['errors'] = [];

    foreach($errors as $error => $value) {
        if((string) $value !== '') $response['errors'][$error] = trim($value);
    }

    $succes = !hasAnyErrrorInRequiredFields($response['errors'], $requiredFields);
    $response['succes'] = $succes;

    return $response;
}

function writeToJson($obj)
{
    $jsonResponse = json_encode($obj, JSON_UNESCAPED_UNICODE, JSON_PRETTY_PRINT);
    header('Content-Type: application/json');

    return $jsonResponse;
}

function sendMail($mail, $stringRespone)
{
    mail($mail, 'answer', $stringRespone);
}


function printArrayInfo(string $name, array $arr): void
{
    echo "{$name}\n";
    echo "________________________________________________\n\n";
    foreach($arr as $key => $value) {
        print_r("{$key} : {$value}\n");
    }
    echo "________________________________________________\n\n";
}

function prettyPrint($json)
{
    $result = '';
    $level = 0;
    $in_quotes = false;
    $in_escape = false;
    $ends_line_level = NULL;
    $json_length = strlen( $json );

    for( $i = 0; $i < $json_length; $i++ ) {
        $char = $json[$i];
        $new_line_level = NULL;
        $post = "";
        if( $ends_line_level !== NULL ) {
            $new_line_level = $ends_line_level;
            $ends_line_level = NULL;
        }
        if ( $in_escape ) {
            $in_escape = false;
        } else if( $char === '"' ) {
            $in_quotes = !$in_quotes;
        } else if( ! $in_quotes ) {
            switch( $char ) {
                case '}': case ']':
                    $level--;
                    $ends_line_level = NULL;
                    $new_line_level = $level;
                    break;

                case '{': case '[':
                    $level++;
                case ',':
                    $ends_line_level = $level;
                    break;

                case ':':
                    $post = " ";
                    break;

                case " ": case "\t": case "\n": case "\r":
                    $char = "";
                    $ends_line_level = $new_line_level;
                    $new_line_level = NULL;
                    break;
            }
        } else if ( $char === '\\' ) {
            $in_escape = true;
        }
        if( $new_line_level !== NULL ) {
            $result .= "\n".str_repeat( "\t", $new_line_level );
        }
        $result .= $char.$post;
    }

    return $result;
}



