<?php

use App\Models\ShortLink;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\InvitationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/use App\Http\Controllers\QRCodeController;

Route::get('/generate-qrcode', [QRCodeController::class, 'generate']);


Route::get('/reception/{reference}', [InvitationController::class, 'voir'])->name('reception');
Route::get('/invitation/{reference}', [InvitationController::class, 'show'])->name('invitation.show');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/i/{code}', function ($code) {
    $link = ShortLink::where('code', $code)->firstOrFail();
    return Redirect::route('invitation.show', ['reference' => $link->reference]);
});
Route::post('/invitations/accept', [InvitationController::class, 'accept']);
Route::post('/invitations/{invitation}/decline', [InvitationController::class, 'decline']);
Route::post('/invitations/{invitation}/close', [InvitationController::class, 'close']);
Route::get('/invitations/{invitation}/download-qrcode', [QRCodeController::class, 'downloadQrCode']);
