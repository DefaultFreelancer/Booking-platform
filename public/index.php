<?php

$gainConfig = require_once '../config/gain.php';


if ($gainConfig['installed'] == false) {


//check requirements.
//if there is any missing extension, redirect to check.php

    $requirements = require_once "requirements.php";

    $has_error = false;
    foreach ($requirements as $key => $$requirement) {
        if (!$requirements[$key]) $has_error = true;
    }


    if ($has_error){
        header('Location: check.php');
        exit();
    }

}


define('LARAVEL_START', microtime(true));


require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';


$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();
$kernel->terminate($request, $response);

