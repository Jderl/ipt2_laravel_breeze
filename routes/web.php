<?php

use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

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

Route::redirect('/', '/students');

// One declaration connects seven resource actions to their HTTP routes.
// CREATE: GET /students/create -> create(); POST /students -> store().
// READ: GET /students -> index(); GET /students/{student} -> show().
// UPDATE: GET /students/{student}/edit -> edit(); PUT/PATCH /students/{student} -> update().
// DELETE: DELETE /students/{student} -> destroy().
Route::resource('students', StudentController::class);
