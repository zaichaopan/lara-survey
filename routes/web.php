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
Route::resource('surveys', 'SurveysController', ['except' => ['edit', 'destroy']]);
Route::resource('surveys.questions', 'QuestionsController', ['except' => ['index', 'destroy']]);
Route::resource('questions.types', 'TypesController', ['only' => ['create','store']]);
Route::resource('surveys.completions', 'CompletionsController', ['only' => ['show', 'create', 'store']]);
Route::resource('surveys.summaries', 'SummariesController', ['only' => ['show']]);
Route::resource('surveys.invitations', 'InvitationsController', ['only' => ['create', 'store']]);
Auth::routes();
