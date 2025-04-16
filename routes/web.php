<?php

use App\Models\ShortLink;
use App\Models\Invitation;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\InvitationController;
use App\Exports\DuplicatesExport;
use Maatwebsite\Excel\Facades\Excel;
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


Route::post('/reception/{reference}', [InvitationController::class, 'confirmation'])->name('reception');
Route::get('/invitation/{reference}', [InvitationController::class, 'show'])->name('invitation.show');

Route::get('/', function () {
    return view('welcome');
});
Route::get('/model', function () {
    $invitation = Invitation::where('reference', "INV-20250328-IK521R")->firstOrFail();

    return view('index',compact('invitation'));
});

Route::get('/i/{code}', function ($code) {
    $link = ShortLink::where('code', $code)->firstOrFail();
    return Redirect::route('invitation.show', ['reference' => $link->reference]);
});
Route::post('/invitations/accept', [InvitationController::class, 'accept']);
Route::post('/invitations/confirmation/', [InvitationController::class, 'confirmation']);
Route::post('/invitations/{invitation}/decline', [InvitationController::class, 'decline']);
Route::post('/invitations/{invitation}/close', [InvitationController::class, 'close']);
Route::get('/invitations/{invitation}/download-qrcode', [QRCodeController::class, 'downloadQrCode']);

Route::get('/clear-cache', function () {
    Artisan::call('optimize:clear');
    return 'Cache cleared';
});
Route::get('/test-user', function () {
    dd(auth()->user());
});
Route::get('/boissons/export-duplicates', function (\Illuminate\Http\Request $request) {
    $json = base64_decode($request->get('data'));
    $duplicates = json_decode($json, true);

    if (empty($duplicates)) {
        return redirect()->back()->with('danger', 'Aucun doublon à exporter.');
    }

    return Excel::download(new DuplicatesExport($duplicates), 'doublons-boissons.xlsx');
})->name('boissons.export-duplicates');
