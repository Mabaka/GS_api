<?php
require_once 'vendor/autoload.php';
use Google\Client;
use Google\Service\Sheets;
use Google\Service\Drive;

try {		

    $email_val = $_POST["email"];
    $tel_val = $_POST["tel"];
    $name_val = $_POST["name"];
    $date = $_POST["date"];
    $result = array("status"=>"","fields"=>[]);

    $tel_val = preg_replace('/[^0-9]/',"",$tel_val);

    $tel_is_correct = strlen($tel_val) == 11;

    $email_correct = preg_match('/^(?:[a-z0-9]+(?:[-_.]?[a-z0-9]+)?@[a-z0-9_.-]+(?:\.?[a-z0-9]+)?\.[a-z]{2,5})$/',$email_val);
    
    $name_is_not_correct = strlen($name_val) > 0 ? preg_match('/[^А-ЯЁа-яё\s]$/u',$name_val) : true;
    $log_text = "Не валидные данные";
    if(!$email_correct){
        $result["status"] = "field_error";    
        array_push($result["fields"],"email");    
        $log_text = $log_text . ' email:' . $email_val;
    }

    if(!$tel_is_correct){
        $result["status"] = "field_error";    
        array_push($result["fields"],"tel");    
        $log_text = $log_text . ' tel:' . $tel_val;
    }

    if($name_is_not_correct){
        $result["status"] = "field_error";
        array_push($result["fields"],"name");
        $log_text = $log_text . ' name:' . $name_val;
    }

    if($email_correct && !$name_is_not_correct && $tel_is_correct){
        $client = new Google\Client();
        $client->setApplicationName('Google Sheets API');

        $client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
        $client->setAccessType('offline');
        $client->setAuthConfig("credentials.json");

        $service = new \Google_Service_Sheets($client);
        $spreadsheetId = '1dq16Y3E9vDPwHUcAB_GsvepyvFQJKER6QvoD_ICNFng';
        $spreadsheet = $service->spreadsheets->get($spreadsheetId);

        $new_rows = [$name_val,$tel_val,$email_val,$date];

        $rows = [$new_rows];

        $valueRange = new \Google_Service_Sheets_ValueRange();
        $valueRange->setValues($rows);        

        $options = ['valueInputOption' => 'USER_ENTERED'];
        $range = 'Лист1';
        $result_of_api = $service->spreadsheets_values->append($spreadsheetId, $range, $valueRange, $options);        
        $log_text = 'Данные валидны';
        $log_text = $log_text . ' Id таблицы: ' . $result_of_api->updates->spreadsheetId;
        $log_text = $log_text . ' Обновлено колонок: ' . $result_of_api->updates->updatedColumns;
        $log_text = $log_text . ' Обновлено клеток: ' . $result_of_api->updates->updatedCells;
        $log_text = $log_text . ' Область обновления: ' . $result_of_api->updates->updatedRange;
        $log_text = $log_text . ' Добавлено строк: ' . $result_of_api->updates->updatedRows;
    }
    
    
    header("Content-Type: application/json");
    echo json_encode($result);

    $date = date('Y-m-d H:m:s.v');

    $log_text = $date . ' ' . $log_text;
	file_put_contents('log.txt', $log_text . PHP_EOL, FILE_APPEND);
    
    exit();
} catch (Exception $e) {
    $log_text = $e->getMessage();
    $date = date('Y-m-d H:m:s.v');
	file_put_contents('log.txt', $date . ' ' . $log_text . PHP_EOL, FILE_APPEND);

    $result["status"] = "server_error";
    header("Content-Type: application/json");
    echo json_encode($result);
}