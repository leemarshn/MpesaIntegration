<?php


/**
 *this is the call back listener: it handles both confirmation and validation requests
 * Created by PhpStorm.
 * User: LeeN
 * Date: 6/29/17
 * Time: 11:58 AM
 */


include_once 'notifications.php';

//sendSMS($gateway,'+254721414836', 'Test sms');
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    //echo "Connection successful";
}


$is_confirmation = false;
$is_validation = false;
$response_message = "";
$xml_message = "";
if (strpos($dataPOST, 'C2BPaymentConfirmationRequest') !== false) {
    $is_confirmation = true;
}
if (strpos($dataPOST, 'C2BPaymentValidationRequest') !== false) {
    $is_validation = true;
}

//This is a validation request
if ($is_validation) {
    $response0= str_replace("soapenv:Envelope","soapenvEnvelope",$dataPOST);
    $response1 = str_replace("<soapenv:Header/>","",$response0);
    $response2 = str_replace("soapenv:Body","soapenvBody",$response1);
    $response3 = str_replace("ns1:C2BPaymentValidationRequest","C2BPaymentValidationRequest",$response2);


    $xmlData = simplexml_load_string($response3);
    $xml_message = $response3;

    $transaction_type = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->TransType;
    $trans_id = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->TransID;
    $trans_time =  (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->TransTime;
    $trans_amount = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->TransAmount;
    $short_code = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->BusinessShortCode;
    $bill_ref_no = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->BillRefNumber;
    $invoice_number = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->InvoiceNumber;
    $mobile_number = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->MSISDN;
    $first_name = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->KYCInfo[0]->KYCValue;
    $last_name = (string)$xmlData->soapenvBody->C2BPaymentValidationRequest->KYCInfo[1]->KYCValue;

    $sql = "SELECT AccountNumber FROM AccountDetails WHERE AccountNumber ='$bill_ref_no'";
    $result = $conn->query($sql);
    if($result->num_rows > 0) {
        $response_message = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> <soapenv:Header/> <soapenv:Body> <c2b:C2BPaymentValidationResult> <ResultCode>0</ResultCode> <ResultDesc>Service processing successful</ResultDesc> <ThirdPartyTransID>2342342</ThirdPartyTransID> </c2b:C2BPaymentValidationResult> </soapenv:Body> </soapenv:Envelope>';
    } else {
        $response_message = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> <soapenv:Header/> <soapenv:Body> <c2b:C2BPaymentValidationResult> <ResultCode>C2B00012</ResultCode> <ResultDesc>Account number is invalid</ResultDesc> <ThirdPartyTransID>2342343</ThirdPartyTransID> </c2b:C2BPaymentValidationResult> </soapenv:Body> </soapenv:Envelope>';
    }
    $conn->close();
}

//This is a confirmation request
if ($is_confirmation) {
    $response0= str_replace("soapenv:Envelope","soapenvEnvelope",$dataPOST);
    $response1 = str_replace("<soapenv:Header/>","",$response0);
    $response2 = str_replace("soapenv:Body","soapenvBody",$response1);
    $response3 = str_replace("ns1:C2BPaymentConfirmationRequest","C2BPaymentConfirmationRequest",$response2);
    $xml_message = $response3;

    $xmlData = simplexml_load_string($response3);
    $transaction_type = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->TransType;
    $trans_id = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->TransID;
    $trans_time =  (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->TransTime;
    $trans_amount = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->TransAmount;
    $short_code = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->BusinessShortCode;
    $bill_ref_no = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->BillRefNumber;
    $invoice_number = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->InvoiceNumber;
    $mobile_number = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->MSISDN;
    $first_name = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->KYCInfo[0]->KYCValue;
    $last_name = (string)$xmlData->soapenvBody->C2BPaymentConfirmationRequest->KYCInfo[1]->KYCValue;

    $date = new DateTime($trans_time);
    $transaction_date = $date->format('Y-m-d H:i a');

//Insert the data into payments table
    $sql = "INSERT INTO payments(TransactionType, TransID, TransTime, TransAmount, BusinessShortCode, BillRefNumber, InvoiceNumber,MobileNumber, FirstName,LastName) 
VALUES ('$transaction_type','$trans_id','$trans_time','$trans_amount','$short_code','$bill_ref_no','$invoice_number','$mobile_number','$first_name','$last_name')";

    if ($conn->query($sql) === TRUE) {
        //echo "Data was inserted";
    }
    else {
//echo "Error: " . $sql . "<br>" . $conn->error;
    }
    $response_message = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:c2b="http://cps.huawei.com/cpsinterface/c2bpayment"> <soapenv:Header/> <soapenv:Body> <c2b:C2BPaymentConfirmationResult>C2B Payment Transaction 1234560000007031 result received.</c2b:C2BPaymentConfirmationResult> </soapenv:Body> </soapenv:Envelope>';

    $smsquery = "SELECT Branch, PhoneNumber FROM AccountDetails WHERE AccountNumber = '$bill_ref_no'";
    $result = $conn->query($smsquery);
    if($result->num_rows > 0) {
        while ($row =  $result->fetch_assoc()) {
            $recipients = $row["PhoneNumber"];
            $branch = $row["Branch"];
            //echo var_dump($row);
        }
        $message = "MPESA $trans_id. $branch outlet has received a payment of KES $trans_amount from $first_name $last_name, mobile number $mobile_number at $transaction_date";
        sendSMS($gateway,$recipients, $message);
    } else {
        //echo "row not exists";
    }
    $conn->close();
}
header("Content-type: text/xml; charset=utf-8");
echo sendResponse($response_message);

