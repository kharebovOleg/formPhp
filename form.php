<?php
require_once 'validator.php';

$validate = new Validator(); //валидатор принимает правила валидации и объект валидации, возвращает массив с ошибками

$keysAndValues = createArrayForValidate($_POST); //массив который будем валидировать
$keys = array_keys($keysAndValues);

$rules = createRules($keys); //правила валидации

$errors = $validate->check($rules, $keysAndValues);

$response = createResponse($errors, $keysAndValues);
$jsonResponse = writeToJson($response);
$stringRespone = json_encode($jsonResponse, JSON_UNESCAPED_UNICODE);

// echo "\n";
// echo 'errors: ';
// print_r($errors);

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

echo "\n";
var_dump($response['succes'] === true ? 'sending response to mail' : 'fail');
if($response['succes'] === true) sendMail($keysAndValues['email'], $stringRespone);

// валидация


// преобразует ключи ['day'], ['month], ['year'] в ключ ['date'], убирает незаполненные необязательные поля
function createArrayForValidate(array $arr): array
{
    $res = $arr;
    $keys = array_keys($arr);
    if(isDateFilled($keys)) {
        $date = createDate();
        $res['date'] = $date;
    }

    $res['phone-number'] = normalizePhoneNumber($res['phone-number']);

    if(in_array('add-number', $keys)) {
        $res['add-number'] = normalizePhoneNumber($res['add-number']);
    }
    $res = deleteEmptyOptionalFields($res);
    // if($res['add-number'] === '') unset($res['add-number']);
    // if($res['patronymic'] === '') unset($res['patronymic']);
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
function isDateFilled(array $keys): bool
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
    $justNums = preg_replace("/[^0-9A-zА-я\+]/", '', $tel); 
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
 * сделать функцию, которая посмотрит на массив errors и если ошибки будут только
 * в необязательных полях, поставит true
 */

function hasAnyErrrorInRequiredFields(array $errors, array $requiredFields): bool
{
    print_r("\nhasAnyErrrorInRequiredFields: \n");
    $res = false;
    foreach($errors as $key => $value) {
        echo 'error: ' . $key;
        if(in_array($key,$requiredFields)) $res = true;
        echo "\n";
        var_dump(in_array($key,$requiredFields));
    }
    echo "\n";
    echo "result: ";
    var_dump($res);
    return $res;
}


//из массива ошибок сделает ответ JSON
function createResponse(array $errors)
{
    $requiredFields = ['email', 'phone-number', 'name', 'sirname'];
    $response = [];
    $succes = false;
    $response['succes'] = $succes;
    $response['errors'] = [];
    foreach($errors as $error => $value) {
        if((string) $value !== '') $response['errors'][$error] = $value;
    }

    $succes = !hasAnyErrrorInRequiredFields($response['errors'], $requiredFields);
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



