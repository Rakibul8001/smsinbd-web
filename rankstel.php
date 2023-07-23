<?php


				
$url='http://api.rankstelecom.com/api/v3/sendsms/json';

$authorization = base64_encode('datahostit:$AxnSxn$2018');
$ch=curl_init($url);
$data=array(
'authentication'=>array('username'=>'datahostit','password'=>'$AxnSxn$2018'),
'messages'=>array(
array('text'=>' আপনার ভেরিফিকেশন কোড  1234  - Albeen', 'sender'=>'8804445641111','datacoding'=>'8',

'recipients'=>array('gsm'=>'8801777333678'),
)
)
);
echo '<pre>';
print_r( $data);
echo '</pre>';
$jsondataencode=json_encode($data);
echo '<pre>';
print_r( $jsondataencode);
echo '</pre>';
CURL_SETOPT($ch,CURLOPT_POST,1);
CURL_SETOPT($ch,CURLOPT_POSTFIELDS,$jsondataencode);
CURL_SETOPT($ch,CURLOPT_HTTPHEADER,array('content-type:application/json'));
$result=CURL_EXEC($ch);
 var_dump($result);

?>