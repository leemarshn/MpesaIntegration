<?php
/**
 * Created by PhpStorm.
 * User: LeeN
 * Date: 6/8/17
 * Time: 9:52 PM
 */
//libxml_use_internal_errors(true);
$myXMLData ="<?xml version='1.0' encoding='UTF-8'?> 
<c2b:C2BPaymentValidationRequest>
         <TransactionType>PayBill</TransactionType>
         <TransID>1234560000007031</TransID>
         <TransTime>20140227082020</TransTime>
         <TransAmount>123.00</TransAmount>
         <BusinessShortCode>12345</BusinessShortCode>
         <BillRefNumber></BillRefNumber>
         <InvoiceNumber></InvoiceNumber>
      </c2b:C2BPaymentValidationRequest>";


$xml = simplexml_load_string($myXMLData) or die("Error: Cannot create object");
    print_r($xml);
