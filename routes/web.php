<?php

use App\Livewire\ChatBotUI;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


Route::get('/chatbot', ChatBotUI::class)->name('chatbot');