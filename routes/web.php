<?php
use Illuminate\Support\Facades\Route;


Route::get('/debug-teste', function() {
    return "Resposta Instantânea!";
});
Route::get('/{any}', function () {
    return view('app'); 
})->where('any', '.*');