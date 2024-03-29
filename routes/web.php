<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;

use App\Http\Controllers\Car\FuelController;
use App\Http\Controllers\Car\ManufacturerController;
use App\Http\Controllers\Car\ModelController;
use App\Http\Controllers\Car\TransmissionController;
use App\Http\Controllers\Car\TypeController;
use App\Http\Controllers\Car\CarController;
use App\Http\Controllers\Car\AccessoriesController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SearchController;
use Illuminate\Support\Facades\App;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!

*/

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes();

Route::get('/home', [CarController::class, 'carlists'])->name('home');
Route::any('/locations/{data?}', [LocationController::class, 'locationlists'])->name('locations');
Route::get('/locations/{id}/show', [LocationController::class, 'show'])->name('locations.show');
Route::any('/drivers/{data?}', [DriverController::class, 'driverlists'])->name('drivers');
Route::get('/driver/{id}', [DriverController::class, 'driverDetails'])->name('driver.details');
Route::post('/search', [SearchController::class, 'search'])->name('global.search');

Route::prefix('fleet/car')->group(function () {
    Route::get('/details/{id}', [CarController::class, 'cardetails'])->name('cardetails');
    Route::get('/search', [CarController::class, 'carsearch'])->name('carsearch');
});


Route::prefix('user')->middleware(['auth'])->group(function () {

    Route::get('/profile', [UserController::class, 'profile'])->name('viewprofile');
    Route::put('/edit/{id}', [UserController::class, 'edit'])->name('editprofile');
    Route::put('change/{id}/password', [UserController::class, 'changePassword'])->name('changePassword');
    Route::get('/addtogarage/{id}', [UserController::class, 'addtousergarage'])->name('addtogarage');
    Route::get('/view/garage', [UserController::class, 'viewusergarage'])->name('viewusergarage');
    Route::post('/save/bookinfo/{id}', [UserController::class, 'savegarage'])->name('savegarage');
    Route::get('/remove/car/garage/{id}', [UserController::class, 'removecargarage'])->name('removecargarage');
    Route::post('/book/car/garage', [UserController::class, 'bookcar']);

    Route::prefix('/booking')->group(function () {

        Route::get('view/pendings', [BookingController::class, 'pendings'])->name('pendings');
        Route::get('/edit/{id}', [BookingController::class, 'editbooking'])->name('edit');
        Route::post('/save/{id}', [BookingController::class, 'savechanges'])->name('savechanges');
        Route::get('/cancel/{id}', [BookingController::class, 'cancel'])->name('cancel');
        Route::get('/view/cancelled', [BookingController::class, 'displaycancelled'])->name('displaycancelled');
        Route::get('/view/confirmed', [BookingController::class, 'confirmed'])->name('confirmed');
        Route::get('/view/finished', [BookingController::class, 'finished'])->name('finished');
        Route::get('/remove/cancelled/{id}', [BookingController::class, 'removecancelled'])->name('removecancelled');
        Route::get('/transaction/invoice/{id}', [BookingController::class, 'print'])->name('print');
    });
});


Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {

    Route::get('/dashboard', [AdminController::class, 'AdminDashboard'])->name('admindashboard');

    Route::view('/location', 'location.index')->name('location.index'); //mp4

    Route::prefix('car')->group(function () {

        Route::resource('manufacturers', ManufacturerController::class);
        Route::post('manufacturer/storeMedia', [ManufacturerController::class, 'storeMedia'])->name('manufacturer.storeMedia');

        Route::resource('types', TypeController::class);
        Route::resource('model', ModelController::class);
        Route::resource('fuel', FuelController::class);
        Route::resource('transmission', TransmissionController::class);

        Route::view('accessories/index', 'car.accessories.index')->name('accessories.page'); //mp3
        Route::view('/drivers/index', 'drivers.index')->name('drivers.page'); //mp2
        Route::view('/index', 'car.index')->name('cars.page'); //mp1
        Route::get('/show/{id}', [CarController::class, 'show'])->name('car.show');
    });

    Route::middleware('blockuser')->group(function () {

        Route::view('/bookings', 'admin.bookings.index')->name('bookings.index');

        Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('createBooking');
        Route::get('/confirm/{id?}', [AdminController::class, 'confirmBooking'])->name('confirmBooking');
        Route::get('/cancel/{id}', [AdminController::class, 'cancellBooking'])->name('cancellBooking');
        Route::post('/bookings/store', [AdminController::class, 'storeBooking'])->name('storeBooking');
        Route::get('/bookings/{id}/edit', [AdminController::class, 'editBooking'])->name('editBooking');
        Route::put('/bookings/{id}/update', [AdminController::class, 'updateBooking'])->name('updateBooking');
        Route::get('/bookings/{id}/delete', [AdminController::class, 'deleteBooking'])->name('deleteBooking');
        Route::get('/bookings/{id}/finished', [AdminController::class, 'finishedBooking'])->name('finishedBooking');

        Route::get('/users/view', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/view/create', [UserController::class, 'createUser'])->name('users.create');
        Route::post('/store/users', [UserController::class, 'storeUser'])->name('users.store');
        Route::get('/edit/{id}/users/', [UserController::class, 'editUser'])->name('users.edit');
        Route::put('/update/{id}/users', [UserController::class, 'updateUser'])->name('users.update');
        Route::delete('/delete/{id}/users', [UserController::class, 'destroyUser'])->name('users.destroy');
    });

    Route::get('report/sales', function () {

        return view('admin.bookings.report');
    })->name('report');
});
