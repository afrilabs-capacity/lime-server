<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\SurveyResponseController;
use App\Http\Controllers\MailingListController;
use App\Http\Controllers\MailingListContactController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ActivityController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/login', [LoginController::class, 'login']);
Route::post('/survey', [SurveyController::class, 'create']);
Route::post('/survey/clone', [SurveyController::class, 'cloneSurvey']);
Route::get('/survey/{uuid}', [SurveyController::class, 'singleSurvey']);
Route::post('/survey/update', [SurveyController::class, 'updateSurvey']);
Route::delete('/survey/delete/{surveyuuid}', [SurveyController::class, 'deleteSurvey']);
Route::get('/surveys', [SurveyController::class, 'index']);
Route::get('/surveys/detached/project/{uuid}', [SurveyController::class, 'detachedSurveys']);
Route::get('/surveys/detached/survey/{uuid}/users', [SurveyController::class, 'detachedUsers']);
Route::post('/survey/distribute', [SurveyController::class, 'distribute']);
Route::post('/survey/attach/user', [SurveyController::class, 'attachUser']);
Route::post('/survey/detach/user', [SurveyController::class, 'detachUser']);
Route::get('/survey/{surveyuuid}/users', [SurveyController::class, 'surveyUsers']);
Route::post('/survey/report', [SurveyController::class, 'getSurveyByDate']);


/*Responses*/
Route::get('/survey/responses/{uuid}', [SurveyResponseController::class, 'singleSurveyResponses']);
Route::post('/survey/response', [SurveyResponseController::class, 'create']);
Route::get('/survey/responses', [SurveyResponseController::class, 'index']);

/*Email List*/
Route::post('/emaill-list/import', [MailingListContactController::class, 'importEmailList']);
Route::post('/email-list/create', [MailingListController::class, 'create']);
Route::get('/email-list/{uuid}', [MailingListController::class, 'singleEmailList']);
Route::get('/email-list', [MailingListController::class, 'index']);


/*Email List Contacts*/
Route::delete('/email-list/delete/contact/{contactuuid}', [MailingListContactController::class, 'deleteEmail']);
Route::get('/email-list/{listuuid}/contacts', [MailingListContactController::class, 'index']);


Route::post('/project/create', [ProjectController::class, 'create']);
Route::get('/project/{uuid}', [ProjectController::class, 'singleProject']);
Route::delete('/project/delete/{uuid}', [ProjectController::class, 'deleteProject']);
Route::get('/projects', [ProjectController::class, 'index']);
Route::get('/projects/all', [ProjectController::class, 'allProjects']);
// Route::post('/project/attach/survey', [ProjectController::class, 'attachSurvey']);
// Route::post('/project/detach/survey', [ProjectController::class, 'detachSurvey']);
Route::get('/project/{projectuuid}/surveys', [ProjectController::class, 'projectSurveys']);


Route::post('/project/attach/user', [ProjectController::class, 'attachUser']);
Route::post('/project/detach/user', [ProjectController::class, 'detachUser']);
Route::get('/project/{projectuuid}/users', [ProjectController::class, 'projectUsers']);

Route::post('/user/create', [UserController::class, 'create']);
Route::get('/users', [UserController::class, 'index']);
Route::get('/user/{uuid}', [UserController::class, 'singleUser']);
Route::post('/user/update', [UserController::class, 'updateUser']);
Route::get('/users/detached/project/{uuid}', [UserController::class, 'detachedUsers']);


Route::get('/roles', [UserController::class, 'allRoles']);



Route::get('/activities', [ActivityController::class, 'index']);
