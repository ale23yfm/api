<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: *');
header('Content-Type: application/json; charset=utf-8');

if (isset($_GET['ID'])) {
  $id = $_GET['ID'];
  $id = urlencode($id);

  require_once '../config.php';

  $core = 'auth';

  $qs = '?';
  $qs = $qs . 'omitHeader=true';
  $qs = $qs . '&';
  $qs = $qs . 'q.op=OR';
  $qs = $qs . '&';
  $qs = $qs . 'q=id%3A';

  $url = 'http://' . $server . '/solr/' . $core . '/select' . $qs . $id;

  $string = @file_get_contents($url);
  if ($string === FALSE) {
    http_response_code(503);
    echo json_encode([
      "error" => "SOLR server in DEV is down",
      "code" => 503
    ]);
    exit;
  }
  $json = json_decode($string);

  if (empty($json->response->docs)) {
      http_response_code(404);
      echo json_encode(["error" => "No user found"]);
      exit;
  }

  unset($json->response->docs[0]->_version_);
  echo json_encode($json->response->docs[0]);
}
