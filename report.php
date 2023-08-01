<?
//часть кода 
// делает запрос к яндекс маркету и получает дату отгрузки 
$token = '';
$oauth_client_id = '';
$authKey = 'OAuth oauth_token='.$token.', oauth_client_id='.$oauth_client_id;
$requestURL = 'https://api.partner.market.yandex.ru/v2/campaigns/22522708/orders/'.$order_ym[2];

// Установка HTTP-заголовков запроса
$headers = array("Authorization: $authKey");  

// // Инициализация c URL
$curl = curl_init();
curl_setopt($curl, CURLOPT_URL, $requestURL);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_HEADER, true);
curl_setopt($curl, CURLINFO_HEADER_OUT, true);

// Выполнение запроса, получение результата
 $result = curl_exec($curl);
 
$test =  explode(' ', htmlspecialchars($result));
 

foreach ($test as   $value) {
    
    if (preg_match('/shipmentDate/', $value)) {        
       $date =  $value;
    }
}


  preg_match('~\d{2}-\d{2}-\d{4}~',$date,$matches);

$date_shipment = $matches[0];
 
curl_close($curl);
