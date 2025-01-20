<?php
require_once 'validator.php';

$keysAndValues = createArrayForValidate($_POST);
$keys = array_keys($keysAndValues);
$errors = [];
$rules = createRules($keys);

echo "\n";
echo 'keysAndValues:';
print_r($keysAndValues);
echo "\n";


echo 'keys:';
print_r($keys);

echo "\n";
echo 'rules:';
print_r($rules);
echo "\n";


echo 'errors:';
print_r($errors);

echo "\n";
echo 'validation:';
$validate = new Validator();

echo "\n";
echo "result:";
$result = $validate->check($rules, $keysAndValues);
print_r($result);


// валидация


// преобразует ключи ['day'], ['month], ['year'] в ключ ['date']
function createArrayForValidate(array $arr): array
{
    $res = $arr;
    $keys = array_keys($arr);
    if(isDateFilled($keys)) {
        $date = createDate();
        $day = array_search('day', $arr);
        $month = array_search('month', $arr);
        $year = array_search('year', $arr);
        $res['date'] = $date;
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

   $time = strtotime("{$day}/{$month}/{$year}");
   $date = date('m-d-Y',$time);
   return $date;
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
            // case 'email':
            //     $res[$key] = 'email;empty';
            //     break;
            // case 'name':
            // case 'sirname':
            //     $res[$key] = 'min:2;max:30;empty';
            //     break;
            case 'patronymic':
                $res[$key] = 'min:2;max:30';
                break;
            // case 'phone-number':
            //     $res[$key] ='phone;empty';
            //     break;
            // case 'add-number':
            //     $res[$key] = 'phone';
            //     break;
            // case 'date':
            //     $res[$key] = 'date';
            //     break;
        }
    }

    return $res;
}

//из массива ошибок сделает ответ JSON
function createResponse(array $data) {
    $response = '';

    return $response;
}

function writeToJson(array $data) {
    $jsonResponse = json_encode($data);
    header('Content-Type: application/json');

    return $jsonResponse;
}

function sendMail($json) {
    
    
}

function isNullOrEmptyString(string|null $str) {
    return $str === null || trim($str) === '';
}


