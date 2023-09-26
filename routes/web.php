<?php

use App\Http\Controllers\UserController;

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

Route::middleware(['auth'])->group(function () {
    Route::get('home', function () {
        return view('home');
    });

    Route::resource('users', UserController::class)->middleware('can:manage:users');

    /*---------------------------------------------------------------
    | Prosegur alarms
    |----------------------------------------------------------------
    */
    Route::get('/prosegur/alarms', function () {
        return view('prosegur.alarms.index');
    })->name('prosegur/alarms')->middleware('can:manage:prosegur');

    Route::get('/thinkific/courses', function () {
        return view('thinkific.courses.index');
    })->name('thinkific/courses')->middleware('can:manage:campus');

    /*---------------------------------------------------------------
    | Campus users
    |----------------------------------------------------------------
    */
    Route::get('/campus/{campus}/users/admins', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.users.admins.index', compact('campus'));
    })->name('campus/users')->middleware('can:manage:campus');

    Route::get('/campus/{campus}/users/teachers', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.users.teachers.index', compact('campus'));
    })->name('campus/users')->middleware('can:manage:campus');

    Route::get('/campus/{campus}/users/students', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.users.students.index', compact('campus'));
    })->name('campus/users')->middleware('can:manage:campus');

    /*---------------------------------------------------------------
    | Campus academic elements
    |----------------------------------------------------------------
    */
    Route::get('/campus/{campus}/academic/programs', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.academic.programs.index', compact('campus'));
    })->name('campus/academic/programs')->middleware('can:manage:campus');
    Route::get('/campus/{campus}/academic/programs/{program}/courses', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        $program = \App\Q10\Models\Programa::find(request()->program);
        return view('campus.academic.programs.courses', compact('campus', 'program'));
    })->name('campus/academic/program/courses')->middleware('can:manage:campus');

    Route::get('/campus/{campus}/academic/terms', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.academic.terms.index', compact('campus'));
    })->name('campus/academic/terms')->middleware('can:manage:campus');

    Route::get('/campus/{campus}/academic/subjects', function () {
        $campus = \App\Q10\Models\Campus::find(request()->campus);
        return view('campus.academic.subjects.index', compact('campus'));
    })->name('campus/academic/subjects')->middleware('can:manage:campus');
});


require __DIR__.'/auth.php';
