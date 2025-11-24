<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DuiVerificationController;

Route::post('/dui/verify', DuiVerificationController::class)->name('api.dui.verify');
