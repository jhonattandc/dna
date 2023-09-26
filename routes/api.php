<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Prosegur\Http\Controllers\AlarmsController;
use App\Q10\Http\Controllers\AdministrativosController;
use App\Q10\Http\Controllers\DocentesController;
use App\Q10\Http\Controllers\EstudiantesController;
use App\Q10\Http\Controllers\ProgramasController;
use App\Q10\Http\Controllers\AsignaturasController;
use App\Q10\Http\Controllers\PeriodosController;

use App\Thinkific\Http\Controllers\CoursesController as ThinkificCoursesController;

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

Route::prefix('prosegur')->group(function () {
    // Alarm routes...
    Route::get('/alarms', [AlarmsController::class, 'index'])->name('prosegur.alarms.index');
});

Route::get('/campus/{campus}/users/admins', [AdministrativosController::class, 'index'])->name('campus.users.admins.index');
Route::get('/campus/{campus}/users/teachers', [DocentesController::class, 'index'])->name('campus.users.teachers.index');
Route::get('/campus/{campus}/users/students', [EstudiantesController::class, 'index'])->name('campus.users.students.index');

Route::get('/campus/{campus}/academic/programs', [ProgramasController::class, 'index'])->name('campus.academic.programs.index');
Route::get('/campus/{campus}/academic/subjects', [AsignaturasController::class, 'index'])->name('campus.academic.subjects.index');
Route::get('/campus/{campus}/academic/terms', [PeriodosController::class, 'index'])->name('campus.academic.terms.index');

Route::get('/thinkific/courses', [ThinkificCoursesController::class, 'index'])->name('thinkific.courses.index');
Route::post('/thinkific/courses/{course}/set', [ThinkificCoursesController::class, 'set'])->name('thinkific.courses.set');

Route::fallback(function (){
    abort(404, 'API resource not found');
});
