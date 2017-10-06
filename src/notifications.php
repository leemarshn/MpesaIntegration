<?php

/**
 *This file is used to send sms notifications to the targeted user in a retail shop.
 * Remember to include the required files from Africas talking
 * Created by PhpStorm.
 * User: LeeN
 * Date: 6/29/17
 * Time: 11:58 AM
 */



$afriusername = "";
$apikey       = "";
require_once('AfricasTalkingGateway.php');
$gateway     = new AfricasTalkingGateway($afriusername, $apikey);

include_once 'config/db.php';

$dataPOST = trim(file_get_contents('php://input'));

function sendResponse($response_message) {
    $response = $response_message;
    return $response;
}

/**
 *This is the function used to send sms to the user
 *call at the end of each conditional statement
 */
function sendSMS($gateway, $recipients, $message)
{
    try {
        //$results = $gateway->sendMessage($recipients, $message, $shortCode, $bulkSMSMode, $options);
        $results = $gateway->sendMessage($recipients, $message);
        foreach ($results as $result) {

            // status is either "Success" or "error message"
            //echo " Number: " . $result->number;
            // echo " Status: " . $result->status;
            // echo " MessageId: " . $result->messageId;
            // echo " Cost: " . $result->cost . "\n";
        }
    }
    catch (AfricasTalkingGatewayException $e) {
        // echo "Encountered an error while sending: " . $e->getMessage();
    }
}
