<?php

$server_list = ['barakat'];

// Define a function to get the last stored timestamp
function getLastTimestamp($serverName)
{
    $timestampFilePath = __DIR__ . '/timestamp/' . $serverName . '.txt';
    if (file_exists($timestampFilePath)) {
        $timestampData = file_get_contents($timestampFilePath);
        $lines = explode("\n", $timestampData);
        foreach ($lines as $line) {
            if (strpos($line, 'Timestamp:') !== false) {
                return strtotime(trim(str_replace('Timestamp:', '', $line)));
            }
        }
    }
    return null;
}

// Define a function to get the last stored timestamp
function getLastTimestampSms($serverName)
{
    $timestampFilePath = __DIR__ . '/timestamp/' . $serverName . '.txt';
    if (file_exists($timestampFilePath)) {
        $timestampData = file_get_contents($timestampFilePath);
        $lines = explode("\n", $timestampData);
        foreach ($lines as $line) {
            if (strpos($line, 'SMS:') !== false) {
                return trim(str_replace('SMS:', '', $line));
            }
        }
    }
    return null;
}

function increaseSmsCount($serverName)
{
    $timestampFilePath = __DIR__ . '/timestamp/' . $serverName . '.txt';

    // Read the file content
    $fileContent = file_get_contents($timestampFilePath);

    // Extract the SMS count
    preg_match('/SMS: (\d+)/', $fileContent, $matches);

    $smsCount = intval($matches[1] ?? 0);


    // Increase the SMS count
    $smsCount = $smsCount + 1;


    // Replace the SMS count in the file content
    $newFileContent = preg_replace('/SMS: \d+/', "SMS: $smsCount", $fileContent);

    // Write the updated content back to the file
    file_put_contents($timestampFilePath, $newFileContent);

    return $smsCount;
}

// Define a function to send an SMS (simulated)
function sendSMS()
{

    // Set the URL
    $url = 'https://bot.boodje.com/sendmessage';

// Create a cURL handle
    $ch = curl_init();

// Set cURL options
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request and store the response
    $response = curl_exec($ch);

// Check for cURL errors
    if (curl_errno($ch)) {
        echo 'Curl error: ' . curl_error($ch);
    }

// Close cURL handle
    curl_close($ch);

// Output the response
    echo $response;
}

// Handle the API check
if (empty($_GET['server']) || !in_array($_GET['server'], $server_list)) {
    die('Unknown Server');
}

$serverName = $_GET['server'];
$lastTimestamp = getLastTimestamp($serverName);

if ($lastTimestamp == null) {
    echo "No stored timestamp found.\n";
    exit;
}
$currentTime = time();
$timeDiff = $currentTime - $lastTimestamp;

if ($timeDiff < 900) { // 900 seconds = 15 minutes
    echo "Not enough time has passed since the last API call.\n";
    exit;
}

if (getLastTimestampSms($serverName) > 3) {

    echo "SMS is sent before.\n";
    exit;
}

sendSMS();
increaseSmsCount($serverName);