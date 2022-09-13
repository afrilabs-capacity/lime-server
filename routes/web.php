<?php

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;


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
    // Role::create(['name' => 'admin']);
    // Role::create(['name' => 'collector']);
    // $admin = User::create(['name' => 'Braimah Attah', 'email' => 'braimahjajke@gmail.com', 'password' => Hash::make("1234")]);
    // $collector = User::create(['name' => 'Dan Sulevan', 'email' => 'codiakes@gmail.com', 'password' => Hash::make("1234")]);
    // $adminRole = Role::findOrFail(1);
    // $admin->assignRole($adminRole);
    // $collectorRole = Role::findOrFail(2);
    // $collector->assignRole($collectorRole);

    return App\Services\SurveyResponseCalculator::calculate(\App\Models\SurveyResponse::get());
    return view('welcome');
});
