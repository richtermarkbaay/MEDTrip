<?php

function connect()
{
    $db_host = 'staging-hca-db.cn3rrwophu7o.us-east-1.rds.amazonaws.com';
    $db_user = 'root';
    $db_password = 'cfe_#602_:D';
    $db_name = 'healthcareabroad';
    
    $mysqli = new \mysqli($db_host, $db_user, $db_password);
    if ($mysqli->connect_error) {
        die('Connect Error (' . mysqli_connect_errno() . ') '. mysqli_connect_error());
    }
    
    return $mysqli;
}