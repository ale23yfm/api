<?php

header("Access-Control-Allow-Origin: *");

$qs = $_SERVER['QUERY_STRING'];

function get_server(){
    //get the IP of the server
    //we need a config file to know where is the SOLR
    require('../../_config/index.php');
    return $server;
}

 $url =  get_server()[0].'shaqodoon/select?'.$qs;
 $json = file_get_contents($url);
 echo $json;
?>
