<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'service' => 'FemiOwoyele.com API',
        'status' => 'ok',
    ]);
});
