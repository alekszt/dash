<?php
define("TOKEN", "5053675550:AAGAEUDCAA7vavC_baBfSMLtTYAjOdIStH8");
define("MYSITE", "https://aleks.zt.ua/api/telegram/");
define("BASE_URL", "https://api.telegram.org/bot" . TOKEN . "/");
define("HOLIDAY_TOKEN", "8323ab1e-ea6a-495f-ab91-727a1ee676e4");
define("HOLIDAY_URL", "https://holidayapi.com/v1/holidays?pretty&key=" . HOLIDAY_TOKEN . "&country=UA&language=uk");

//https://api.telegram.org/bot5053675550:AAGAEUDCAA7vavC_baBfSMLtTYAjOdIStH8/setWebhook?url=https://aleks.zt.ua/api/telegram/
//namebot alekseyztbot
//https://holidayapi.com/v1/holidays?pretty&key=8323ab1e-ea6a-495f-ab91-727a1ee676e4&country=UA&year=2021&month=05&day=02

$update = json_decode(file_get_contents('php://input'));// Доступ к различным потокам ввода-вывода

file_put_contents('logs.txt', print_r($update, true), FILE_APPEND);
$chat_id = $update->message->chat->id ?? '';
$text= $update->message->text ?? '';

if ($text == '/start') {
  $res = send_request('sendMessage', [
    'chat_id' => $chat_id,
    'text' => 'Доброго дня. Це мій перший бот',
  ]);
}
elseif ($text == '1') {
  $res = send_request('sendDice', [
    'chat_id' => $chat_id,
  ]);
}
elseif ($text == '2') {
  	$res = send_request('sendLocation', [
    'chat_id' => $chat_id,
    'latitude' => 50.2566909,
    'longitude' => 28.6535048,
  ]);
}
elseif ($text == '3') {
  	$res = send_request('sendPoll', [
    'chat_id' => $chat_id,
    'question' => "Опитування",
    'options' => '["Питання 1", "Питання 2", "Питання 3"]',
  ]);
}
elseif ($text == '4') {
  	$res = send_request('copyMessage', [
    'chat_id' => $chat_id,
    'from_chat_id' => $chat_id,
    'message_id' => 139,
    'caption' => "Caption",
  ]);
}
elseif ($text == '5') {
  	$res = send_request('sendContact', [
    'chat_id' => $chat_id,
    'phone_number' => "0967053580",
    'first_name' => "Aleksey",
    'last_name' => "Kravchuk",
  ]);
}
elseif ($text == '6') {
  	$res = send_request('editMessageLiveLocation', [
    'chat_id' => $chat_id,
    'latitude' => 50.2566909,
    'longitude' => 28.6535048,
  ]);
}
elseif ($text == '7') {
  	$res = send_request('sendAnimation', [
    'chat_id' => $chat_id,
    'animation' => 'https://aleks.zt.ua/telegram/smile.png',
  ]);
}
elseif ($text == '8') {
  $res = send_request('sendPhoto', [
    'chat_id' => $chat_id,
    'photo' => 'https://aleks.zt.ua/telegram/img_t.jpg',
  ]);
}
elseif (preg_match("#^([0-9]{1,2})-([0-9]{1,2})-([0-9]{4})$#", $text, $matches)){
  $holidayss = json_decode(file_get_contents(HOLIDAY_URL . "&year={$matches[3]}&month={$matches[2]}&day={$matches[1]}"), true);
  //$holidays = json_decode(file_get_contents("https://holidayapi.com/v1/holidays?pretty&key=8323ab1e-ea6a-495f-ab91-727a1ee676e4&country=UA&year=2021&month=05&day=02"));
  //$holidayss = json_decode(file_get_contents("https://holidayapi.com/v1/holidays?pretty&key=8323ab1e-ea6a-495f-ab91-727a1ee676e4&country=UA&year=2021&month=01&day=01"), true);
  file_put_contents('h_logs.txt', print_r($holidayss, true), FILE_APPEND);
  $result = $holidayss['holidays'][0]['name'];;
  $res = send_request('sendMessage', [
    'chat_id' => $chat_id,
    'text' => $result,
  ]);
}
else {
  list($firstName, $lastName, $threeName) = explode(" ", $text);
  $conn = mysqli_connect("localhost", "aleks", "w4lcom4", "er");
  $sql = "SELECT * FROM sluhach WHERE firstName = '$firstName' AND lastName = '$lastName' AND threeName = '$threeName'";
  $result = mysqli_query($conn, $sql);
  $mas = "";
  if (mysqli_num_rows($result) > 0) {
    // output data of each row
    while($row = mysqli_fetch_assoc($result)) {
      $mas .= "Слухач: " . $row["firstName"] . " " . $row["lastName"] . " " . $row["threeName"] ."\n";
    	$mas .= "Заклад: " . $row["zaklad"] . "\n";
    	$mas .= "Сертифікат: " . $row["num_sertifikat"] . "\n\n";
    }
  } else {
  $mas = "Не знайдено";
  }
  mysqli_close($conn);
  $res = send_request('sendMessage', [
    'chat_id' => $chat_id,
    'text' => $mas,
  ]);
}

function send_request($method, $params = [])
{
  if(!empty($params)) {
    $url = BASE_URL . $method . '?' . http_build_query($params);
  } else {
    $url = BASE_URL . $method;
  }
  return json_decode(file_get_contents($url));
}
?>
