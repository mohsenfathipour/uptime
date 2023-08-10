<?php
date_default_timezone_set('Asia/Tehran');
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


function sendSms($mobile, $server)
{
    $path = 'https://api.kavenegar.com/v1/39436C63716F4844736D674B4C2B71724E426F6F6138486271387A704F5A496F/verify/lookup.json';

    $params = [
        "receptor" => $mobile,
        "template" => "monitoringSystem",
        "token" => $server
    ];

    $queryString = http_build_query($params);
    $fullPath = $path . '?' . $queryString;

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $fullPath);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        // Handle cURL error
        echo 'cURL Error: ' . curl_error($ch);
    } else {
        $res = json_decode($response, true);
        // Process the $res variable as needed
    }

    curl_close($ch);
}


// Define a function to send an SMS (simulated)
function alert($serverName)
{

    sendSms('09125676987', $serverName);
    sendSms('09352886868', $serverName);

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
    $serverName = 'barakat';
} else {
    $serverName = $_GET['server'];
}

$lastTimestamp = getLastTimestamp($serverName);

if ($lastTimestamp == null) {
    echo "No stored timestamp found.\n";
    exit;
}
$currentTime = time();
$timeDiff = $currentTime - $lastTimestamp;

if ($timeDiff < 300) { // 900 seconds = 15 minutes
    echo "Not enough time has passed since the last API call.\n";
    exit;
}

if (getLastTimestampSms($serverName) > 3) {

    echo "SMS is sent before.\n";
    exit;
}

alert($serverName);
increaseSmsCount($serverName);