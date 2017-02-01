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


//you have to include the next scripts to make sure that you are able to access this plugin from other websites or from mobile apps
include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

global $wpdb ;


//create a table (if not exists )which you will use later to save the list of registered devices
$wpdb->get_results("create table  if not exists gcm_tokens ( token varchar(255) NOT NULL PRIMARY KEY UNIQUE);");

//insert the the token after a $GET request
$wpdb->get_results("insert into gcm_tokens values('". $_GET['request']."') ") ;


//the next line is an event hook (listenner ) which will call the send push function which is responsible for sending push notifications 
add_action('publish_post', 'sendPush');

function sendPush()
{
global $wpdb ;

$result = $wpdb->get_results(" SELECT * FROM gcm_tokens ;") ; 
//for each registered user in the gcm_tokens table we will send a push notification
    foreach ($result as $row) {
$to = $row->token; 
send($to);  // a  call for the send function
}

}

//function to send a notification to the user with the token stored in $to


function send($to)

{
global $wpdb ;

	//post title
    $title = get_post()->post_title;
	//post message
    $message = get_post()->post_title;
   

// API access key from Google API's Console

	$gcm_key = 'your key' ; // if you don't know how to get a one check this link : http://www.connecto.io/kb/knwbase/getting-gcm-sender-id-and-gcm-api-key/

    define('API_ACCESS_KEY', $gcm_key);

    $registrationIds = array($to);  

	
	//configure your push notification
    $msg = array

    (

        'message' => $message,

        'title' => $title,

        'vibrate' => 1,

        'sound' => 1 , 
	'extra' =>get_post()->ID


// you can also add images, additionalData

    );

	
	
	//the rest is about sending the push notifcation via GCM servers
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



