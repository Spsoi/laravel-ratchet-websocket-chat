<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/test', function () {
    return view('test');
});

Route::get('/rooms', function () {
    return view('rooms');
});

Route::get('/room', function (\Illuminate\Http\Request $request) {
    if ($request->input('id') == 1) {
        $roomName = 'one';
    }
    if ($request->input('id') == 2) {
        $roomName = 'two';
    }
    if ($request->input('id') == 3) {
        $roomName = 'three';
    }
    return view('room', 
        [
            'id' => $request->input('id'), 
            'room_name' => $roomName,
            'name' => $request->input('name')
        ]);
});