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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/surveys/create', 'SurveysController@create')->name('surveys.create');
Route::get('/surveys/{survey}', 'SurveysController@show')->name('surveys.show');
Route::post('/surveys/{survey', 'SurveysController@store')->name('surveys.store');
Route::post('/surveys/{survey}/completions', 'CompletionsController@store')->name('completions.store');

// Questions
Route::get('/surveys/{survey}/questions/create', 'QuestionsController@create')->name('questions.create');
Route::get('/surveys/{survey}/questions/{question}/edit', 'QuestionsController@edit')->name('questions.edit');
Route::post('/surveys/{survey}/questions', 'QuestionsController@store')->name('questions.store');

Route::get('/completions/{completion}', 'CompletionsController@show')->name('completions.show');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');