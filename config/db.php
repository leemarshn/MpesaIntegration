<?php
/**
 * Created by PhpStorm.
 * User: LeeN
 * Date: 6/16/17
 * Time: 11:58 AM
 */



//DB details
$dbHost = 'localhost';
$dbUsername = '';
$dbPassword = '';
$dbName = '';

//Create connection and select DB
$conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

if ($conn->connect_error) {
    die("Unable to connect database: " . $conn->connect_error);
}
