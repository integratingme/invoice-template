<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

Route::get('/', [InvoiceController::class, 'index'])->name('invoice.generator');
Route::post('/generate-pdf', [InvoiceController::class, 'generatePdf'])->name('invoice.pdf');
