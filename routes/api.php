<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FideleController;

Route::get('/fideles', [FideleController::class, 'index']);