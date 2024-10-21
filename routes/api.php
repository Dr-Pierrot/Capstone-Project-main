<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\StudentController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [RegisterController::class, 'register']);
Route::post('/login', [LoginController::class, 'loginApi']);
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::get('/students', [StudentController::class, 'getStudentApi'])->name('api.students.getStudents');
Route::middleware('auth:sanctum')->get('/students', [StudentController::class, 'getStudentApi'])->name('api.students.getStudents');

Route::get('/subjects', [SubjectController::class, 'getSubjectApi'])->name('api.subjects.getSubjects');
Route::middleware('auth:sanctum')->get('/subjects', [SubjectController::class, 'getSubjectApi'])->name('api.subjects.getSubjects');

Route::get('/sections', [SectionController::class, 'getSectionApi'])->name('api.sections.getSections');
Route::middleware('auth:sanctum')->get('/sections', [SectionController::class, 'getSectionApi'])->name('api.sections.getSections');

Route::post('password/forgot', [ForgotPasswordController::class, 'sendResetLinkEmailApi']);
Route::post('password/reset', [ResetPasswordController::class, 'resetPasswordApi']);