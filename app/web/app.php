<?php

use Test\lib\App;
use Test\lib\Request;

require_once '../vendor/autoload.php';

$app = new App();

$request = Request::create();
$response = $app->handle($request);
$response->send();
$app->close();

exit;
