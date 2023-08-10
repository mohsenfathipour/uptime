<?php
date_default_timezone_set('Asia/Tehran');

$server_list = ['barakat'];

// Define a function to store the timestamp in a file
function storeTimestamp($serverName) {
    $timestamp = time();
    $data = "Timestamp: " . date('Y-m-d H:i:s', $timestamp) . "\nServer Name: $serverName" . "\nSMS: 0\n\n";
    $timestampFilePath = __DIR__ . '/timestamp/'. $serverName .'.txt';
    file_put_contents($timestampFilePath, $data);
    return $timestamp;
}

// Handle the GET request
if(empty($_GET['server']) || !in_array($_GET['server'],$server_list)) {
    die('Unknown Server');
}

$storedTimestamp = storeTimestamp($_GET['server']);
echo "Timestamp stored: " . date('Y-m-d H:i:s', $storedTimestamp) . "\n";

