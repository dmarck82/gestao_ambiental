<?php

function connect_local_mysqli($database = NULL, $charset = "utf8")
{
    $host = 'localhost'; 
    $username = 'gestam16_douglas';
    $password = 'R3sende!23';
    $database = 'gestam16_gestao_ambiental';
   $conn = new mysqli($host, $username, $password, $database);
    return $conn;
}
