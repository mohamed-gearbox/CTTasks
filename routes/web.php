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

Route::get('/', 'App\Http\Controllers\TaskManagerController@home')->name('tasks.home');
Route::post('/save/task', 'App\Http\Controllers\TaskManagerController@save')->name('task.save');
Route::post('/task/priority', 'App\Http\Controllers\TaskManagerController@changePriority')->name('task.priority');
Route::post('/delete/task', 'App\Http\Controllers\TaskManagerController@delete')->name('task.delete');
