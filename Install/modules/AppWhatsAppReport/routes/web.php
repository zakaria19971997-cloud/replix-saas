<?php

use Illuminate\Support\Facades\Route;
use Modules\AppWhatsAppReport\Http\Controllers\AppWhatsAppReportController;

Route::middleware(['auth'])->group(function () {
    Route::prefix('app/whatsapp/report')->group(function () {
        Route::get('/', [AppWhatsAppReportController::class, 'index'])->name('app.whatsappreport.index');
    });
});
