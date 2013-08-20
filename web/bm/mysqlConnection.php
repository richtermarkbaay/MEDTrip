<?php

include "include.php";

$mysql = connect();
echo $mysql->host_info;

$mysql->close();