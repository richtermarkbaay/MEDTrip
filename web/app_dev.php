<?php

use Symfony\Component\HttpFoundation\Request;

// If you don't want to setup permissions the proper way, just uncomment the following PHP line
// read http://symfony.com/doc/current/book/installation.html#configuration-and-setup for more information
//umask(0000);

// This check prevents access to debug front controllers that are deployed by accident to production servers.
// Feel free to remove this, extend it, or make something more sophisticated.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array(
        '127.0.0.1',
        '112.199.81.42',
        '::1',
    ))
) {
    header('HTTP/1.0 403 Forbidden');
    exit('You are not allowed to access this file.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';
$mainStart = \microtime(true);
$start = \microtime(true);
$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$end = \microtime(true); 
$initDiff = $end-$start;

$start = \microtime(true);
$response = $kernel->handle($request);
$end = \microtime(true); 
$responseDiff = $end-$start;  

$start = \microtime(true);
$startBeforeSend = \microtime(true);
// echo "INIT: {$initDiff}s RESPONSE: {$responseDiff}s ";
// exit;
$response->send();

$end = \microtime(true); 
$responseSendDiff = $end-$start; 
$total = $end-$mainStart;

$kernel->terminate($request, $response);


// if (defined('GLOBAL_WATA')){
//     var_dump(GLOBAL_WATA);
// }
// echo "AA START TIME:{$startBeforeSend}s INIT: {$initDiff}s RESPONSE: {$responseDiff}s RESPONSE SEND: {$responseSendDiff}s TOTAL: {$total}s";
// echo "<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
// exit;
