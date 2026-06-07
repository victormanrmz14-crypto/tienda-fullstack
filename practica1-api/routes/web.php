<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;

// Broadcast authentication for private channels
Broadcast::routes(['middleware' => ['auth:sanctum']]);

Route::get('/', function () {
    return view('welcome');
});
