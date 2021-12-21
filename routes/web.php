<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DocusignController;
use App\Http\Controllers\DocusignExampleController;
use App\Http\Controllers\DocusignSendEnvelopeController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});


Route::get('/first', [DocusignController::class, 'index']);

Route::get('/docusign', [DocusignController::class, 'testTuckerEric']);

Route::get('/docusign/recipient', [DocusignController::class, 'testAddRecipient']);
