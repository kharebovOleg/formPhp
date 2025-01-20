<?php
require_once 'validator.php';

$validate = new Validator();
$keysAndValues = createArrayForValidate($_POST);
$keys = array_keys($keysAndValues);
$rules = createRules($keys);
$errors = $validate->check($rules, $keysAndValues);
$response = createResponse($errors, $keysAndValues);
$jsonResponse = writeToJson($response);
$stringRespone = json_encode($jsonResponse, JSON_UNESCAPED_UNICODE);

if($response['succes'] === true) sendMail($keysAndValues['email'], $stringRespone);



echo "\n";
echo "errors: ";
print_r($errors);

echo "\n";
echo 'keysAndValues: ';
print_r($keysAndValues);

echo "\n";
echo 'rules:';
print_r($rules);

echo "\n";
echo 'response: ';
var_dump($response);


echo "\n";
echo 'json: ';
print_r($jsonResponse);

echo "\n";
echo 'string: ';
print_r($stringRespone);



// валидация


// преобразует ключи ['day'], ['month], ['year'] в ключ ['date']
function createArrayForValidate(array $arr): array
{
    $res = $arr;
    $keys = array_keys($arr);
    if(isDateFilled($keys)) {
        $date = createDate();
        $res['date'] = $date;
    }
    if(in_array('phone-number', $keys)) {
        $res['phone-number'] = normalizePhoneNumber($res['phone-number']);
    }
    if(in_array('add-number', $keys)) {
        $res['add-number'] = normalizePhoneNumber($res['add-number']);
    }
    unset($res['day']);
    unset($res['month']);
    unset($res['year']);
    return $res;
}

// все ли ключи ['day'], ['month], ['year'] есть
function isDateFilled(array $keys): bool
{
    return in_array('day',$keys) && in_array('month',$keys) && in_array('year',$keys);
}

function createDate()
{
   $day = $_POST['day'];
   $month = normalizeMonth($_POST['month']);
   $year = $_POST['year'];

//    $time = strtotime("{$day}/{$month}/{$year}");
//    $date = date('m-d-Y',$time);
    $date = "{$day}-{$month}-{$year}";
    return $date;
}

function normalizePhoneNumber($phone)
{
    $tel = trim((string) $phone);
    $justNums = preg_replace("/[^0-9A-z]/", '', $tel);
    $changedPlusSeven = str_replace("+7", '8', $justNums);
    return $changedPlusSeven;
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

// создать правила для валидации
function createRules(array $keys): array
{
    $res = [];
    foreach($keys as $key) {
        switch($key) {
            case 'email':
                $res[$key] = 'email:0;empty:0';
                break;
            case 'name':
            case 'sirname':
                $res[$key] = 'min:2;max:20;empty:0';
                break;
            case 'patronymic':
                $res[$key] = 'min:2;max:20';
                break;
            case 'phone-number':
                $res[$key] ='max:11;min:11;empty:0;phone:0';
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

//из массива ошибок сделает ответ JSON
function createResponse(array $errors)
{
    $response = [];
    $succes = false;
    $response['succes'] = $succes;
    $response['errors'] = [];
    $succes = false;
    foreach($errors as $error => $value) {
        if((string) $value !== '') $response['errors'][$error] = $value;
    }
    if(array_key_exists('add-number', $response['errors']) && count($response['errors']) === 1) {
        $succes = true;
    } else {
        $succes = count($response['errors']) === 0 ? true : false;
    }
    $response['succes'] = $succes;
    

    return $response;
}

function writeToJson($obj)
{
    $jsonResponse = json_encode($obj, JSON_UNESCAPED_UNICODE);
    header('Content-Type: application/json');

    return $jsonResponse;
}

function sendMail($mail, $stringRespone)
{
    mail($mail, 'answer', $stringRespone);
}

// function isNullOrEmptyString(string|null $str) {
//     return $str === null || trim($str) === '';
// }


