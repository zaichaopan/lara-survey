<?php

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

Route::view('/', 'welcome');
Route::get('/home', 'HomeController@index')->name('home');
Route::resource('surveys', 'SurveysController', ['only' => ['create', 'show', 'store']]);
Route::resource('surveys.questions', 'QuestionsController', ['only' => ['create', 'edit', 'store', 'update']]);
Auth::routes();
