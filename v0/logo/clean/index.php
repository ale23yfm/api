<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

require_once '../../config.php';

$core = 'auth'; // Core name
$command = '/update';

$qs = '?_=' . time(); // Use current time as a cache buster
$qs .= '&commitWithin=1000';
$qs .= '&wt=json';

$url = 'http://' . $server . '/solr/' . $core . $command . $qs;

// Get the required parameters
$id = $_GET['id'] ?? ''; // Document ID to update
$field = 'url';          // Field to remove (hardcoded for this endpoint)

if (empty($id)) {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Document ID is required', 'code' => 400]);
    exit;
}

// Create the atomic update payload
$data = json_encode([
    [
        "id" => $id,
        $field => ["set" => null] // Use Solr's atomic update syntax to remove the field
    ]
]);

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($data)
]);

$response = curl_exec($ch);

if ($response === false) {
    $error_message = curl_error($ch);
    curl_close($ch);
    header("HTTP/1.1 503 Service Unavailable");
    echo json_encode(['error' => 'Failed to execute request: ' . $error_message, 'code' => 503]);
    exit;
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($http_code >= 400) {
    header("HTTP/1.1 " . $http_code . " Bad Request");
    echo $response;
    exit;
}

echo $response;
?>
