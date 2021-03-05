<?php

use Illuminate\Support\Facades\Route;
use Tinkeshwar\Imager\Http\Controllers\ImagerController;

Route::get('thumb/{id}/{height}/{width}',[ImagerController::class, 'index']);
