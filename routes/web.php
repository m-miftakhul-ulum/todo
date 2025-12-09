<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {

    $todos = \App\Models\Todo::all();

    return view('welcome', compact('todos'));
});

Route::resource("todos", TodoController::class);
