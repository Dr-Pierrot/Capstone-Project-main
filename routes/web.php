<?php

use App\Exports\AttendanceExport;
use App\Exports\ClassStandingExport;
use App\Exports\ExamsExport;
use App\Mail\Mailing;
use App\Exports\PeriodicGradesExport;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\ChangePasswordController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\ClassCardController;
use App\Http\Controllers\AttendanceController;
use App\Exports\StudentsExport;
use App\Models\Attendance;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Calculation\Financial\CashFlow\Constant\Periodic;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::get('/', function () {
    return redirect()->route('login');
});


Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


// routes/web.php

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Only admin can access registration routes
    Route::middleware(['admin'])->group(function () {
        Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
        Route::post('/register', [RegisterController::class, 'register']);
        
        // Users Management
        Route::get('/users', [RegisterController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [RegisterController::class, 'update']);
        Route::delete('/users/{user}', [RegisterController::class, 'destroy']);
        

    });
    Route::get('/sections', [SectionController::class, 'index'])->name('sections.index');
    Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');
    Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');
    Route::delete('/sections/{section}', [SectionController::class, 'destroy'])->name('sections.destroy');
    

    // Subjects
    Route::get('/subjects', [SubjectController::class, 'index'])->name('subjects.index');
    // Route::get('/subjects/create', [SubjectController::class, 'create'])->name('subjects.create');
    Route::post('/subjects', [SubjectController::class, 'store'])->name('subjects.store');
    // Route::get('/subjects/{subject}/edit', [SubjectController::class, 'edit'])->name('subjects.edit');
    Route::put('/subjects/{subject}', [SubjectController::class, 'update'])->name('subjects.update');
    Route::delete('/subjects/{subject}', [SubjectController::class, 'destroy'])->name('subjects.destroy');

    // Other authenticated routes...

    route::get('/students', [StudentController::class, 'index'])->name('students.index');
    route::get('students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('students/store', [StudentController::class, 'store'])->name('students.store');
    Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');
    route::post('/students/upload-csv', [StudentController::class, 'uploadCSV'])->name('students.uploadCSV');});
    Route::delete('/students/{student}', [StudentController::class, 'destroy'])->name('students.destroy');
    // Route::put('/shuffle', [StudentController::class, 'shuffleStudent'])->name('students.shuffle');
    Route::match(['get', 'post'], '/shuffle', [StudentController::class, 'shuffleStudent'])->name('students.shuffle');
    Route::match(['get', 'post'], '/group-shuffle', [StudentController::class, 'groupShuffle'])->name('students.group.shuffle');


    Route::get('export-students', function () {
        $teacherId = auth()->user()->id;
        return Excel::download(new StudentsExport($teacherId), 'students.xlsx');
    })->name('students.exportStudents');
    Route::get('export-attendances', function () {
        $teacherId = auth()->user()->id;
        return Excel::download(new AttendanceExport($teacherId), 'attendances.xlsx');
    })->name('students.exportAttendances');
    Route::get('export-class-standings', function () {
        $teacherId = auth()->user()->id;
        return Excel::download(new ClassStandingExport($teacherId), 'class-standings.xlsx');
    })->name('students.exportClassStandings');
    Route::get('export-exams', function () {
        $teacherId = auth()->user()->id;
        return Excel::download(new ExamsExport($teacherId), 'exams.xlsx');
    })->name('students.exportExams');
    Route::get('export-periodic', function () {
        $teacherId = auth()->user()->id;
        return Excel::download(new PeriodicGradesExport($teacherId), 'periodic-grades.xlsx');
    })->name('students.exportPeriodicGrades');


    Route::get('/class-card', [ClassCardController::class, 'index'])->name('class-card.index');
    Route::get('/class-card/filter-students', [ClassCardController::class, 'filterStudents'])->name('class-card.filter-students');
    Route::post('/class-card/performance-task/store', [ClassCardController::class, 'performanceTaskStore'])->name('class-card.performance-task.store');
    Route::put('/class-card/performance-task/update/{score}', [ClassCardController::class, 'performanceTaskUpdate'])->name('class-card.performance-task.update');
    Route::delete('/class-card/performance-task/delete', [ClassCardController::class, 'performanceTaskBulkDelete'])->name('class-card.performance-task.bulkDelete');


    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.index');
    Route::post('/attendance/store', [AttendanceController::class, 'store'])->name('attendance.store');


// Forgot Password Routes
Route::get('change-password', [ChangePasswordController::class, 'showChangePasswordForm'])->name('password.change');
Route::post('change-password', [ChangePasswordController::class, 'updatePassword'])->name('password.updates');
Route::get('forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@showForgotPasswordForm')->name('password.request');
Route::post('forgot-password', 'App\Http\Controllers\Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');

// Reset Password Routes
Route::get('reset-password/{token}/{email}', 'App\Http\Controllers\Auth\ResetPasswordController@showResetPasswordForm')->name('password.reset');
Route::post('reset-password', 'App\Http\Controllers\Auth\ResetPasswordController@resetPassword')->name('password.update');

