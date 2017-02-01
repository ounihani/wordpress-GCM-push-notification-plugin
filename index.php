<?php

/*
 *
 * Plugin Name: news app
 * Plugin URI:
 * Version: 0.0.1
 * Author: OuniHani
 * Description: this plugin is responsible for sending data from MYSQL data base to mobile apps
 * and notify user's for new posts...
 *
*/


$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb ;



$wpdb->get_results("create table  if not exists gcm_tokens ( token varchar(255) NOT NULL PRIMARY KEY UNIQUE);");

$wpdb->get_results("insert into gcm_tokens values('". $_GET['request']."') ") ;



add_action('publish_post', 'sendPush');



 







function sendPush()
{
global $wpdb ;
$result = $wpdb->get_results(" SELECT * FROM gcm_tokens ;") ; 

    foreach ($result as $row) {
$to = $row->token; 
send($to);
}

}


function send($to)

{
global $wpdb ;

 
    $title = get_post()->post_title;

    $message = get_post()->post_title;
   

// API access key from Google API's Console

// replace API

    define('API_ACCESS_KEY', 'AIzaSyADn21lddOXIwf2FMeWTk8yzGUsCREnS3k');

    $registrationIds = array($to);

    $msg = array

    (

        'message' => $message,

        'title' => $title,

        'vibrate' => 1,

        'sound' => 1 , 
	'extra' =>get_post()->ID


// you can also add images, additionalData

    );

    $fields = array

    (

        'registration_ids' => $registrationIds,

        'data' => $msg

    );

    $headers = array

    (

        'Authorization: key=' . API_ACCESS_KEY,

        'Content-Type: application/json'

    );

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');

    curl_setopt($ch, CURLOPT_POST, true);

    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

    $result = curl_exec($ch);

    curl_close($ch);


}


?>



