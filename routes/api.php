<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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











Route::get('/courses', [CourseController::class, 'index']);
Route::get('/courses/search', [CourseController::class, 'search'])->name('courses.search');
Route::post('/login', [AuthController::class, 'login']);
Route::post('forgot/password', [AuthController::class, 'forgotPassword']);
Route::post('/register', [AuthController::class, 'register']);


Route::middleware(['auth:sanctum', 'role:loggedIn'])->group(function () {
    Route::post('/payments', [PaymentController::class, 'store'])->name('payments.store');
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::post('/upload', function (Request $request) {
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $name = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('images'), $name);
            return response()->json(['message' => 'Uspešno ste poslali fotografiju']);
        } else {
            return response()->json(['message' => 'Data not found'])->error();
        }
    });
});



Route::group(['middleware' => ['auth:sanctum', 'admin']], function () {
    Route::put('/courses/{id}/update', [CourseController::class, 'update']);
    Route::delete('/courses/{id}/delete', [CourseController::class, 'delete']);
    Route::post('/courses/insert', [CourseController::class, 'insert']);
    Route::post('/admin/logout', [AuthController::class, 'logout']);
});






Route::get('/users/{id}', [UserController::class, 'show'])->name('user.show'); //prikaz korisnickog profila
Route::get('/courses', [CourseController::class, 'index'])->name('courses.index'); //prikaz svih kurseva
Route::get('/courses/{id}', [CourseController::class, 'show'])->name('courses.show'); //prikaz pojedinacnog kursa
Route::get('/payments/create', [PaymentController::class, 'create'])->name('payments.create'); //kreiranje nove uplate
 //cuvanje nove uplate
Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index'); //prikaz svih uplata
Route::get('/payments/{id}', [PaymentController::class, 'show'])->name('payments.show'); //prikaz pojedinacne uplate
Route::get('/payments/{id}/add-courses', [PaymentController::class, 'addCoursesForm'])->name('payments.add_courses_form'); //dodavanje kusreva uplati, forma
Route::post('/payments/{id}/add-courses', [PaymentController::class, 'addCourses'])->name('payments.add_courses'); //cuvanje kurseva za uplatu
Route::get('/users', [UserController::class, 'index'])->name('user.index'); //prikaz svih korsnika


