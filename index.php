<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Laravel Root Index
|--------------------------------------------------------------------------
| This version of index.php allows Laravel to run from the root directory
| instead of the /public folder.
|
| Make sure all paths below point correctly to /vendor and /bootstrap/app.php
*/

define('LARAVEL_START', microtime(true));

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
