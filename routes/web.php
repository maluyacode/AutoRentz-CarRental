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
// Route::post('/locations/{data?}', [LocationController::class, 'locationlists'])->name('locations');
Route::get('/drivers/{data?}', [DriverController::class, 'driverlists'])->name('drivers');
Route::post('/drivers/{data?}', [DriverController::class, 'driverlists'])->name('drivers');
Route::get('/driver/{id}', [DriverController::class, 'driverDetails'])->name('driver.details');
Route::post('/search', [SearchController::class, 'search'])->name('global.search');

// for Listing, Viewing details
Route::prefix('fleet/car')->group(function () {
    Route::get('/details/{id}', [CarController::class, 'cardetails'])->name('cardetails');
    Route::get('/search', [CarController::class, 'carsearch'])->name('carsearch');
});

//User Routes
Route::prefix('user')->middleware(['auth'])->group(function () {
    Route::get('/profile', [UserController::class, 'profile'])->name('viewprofile');
    Route::put('/edit/{id}', [UserController::class, 'edit'])->name('editprofile');
    Route::put('change/{id}/password', [UserController::class, 'changePassword'])->name('changePassword');
    Route::get('/addtogarage/{id}', [UserController::class, 'addtousergarage'])->name('addtogarage');
    Route::get('/view/garage', [UserController::class, 'viewusergarage'])->name('viewusergarage');
    Route::get('/edit/garage/car/{id}', [UserController::class, 'editgarage'])->name('editgarage');
    Route::post('/save/bookinfo/{id}', [UserController::class, 'savegarage'])->name('savegarage');
    Route::get('/remove/car/garage/{id}', [UserController::class, 'removecargarage'])->name('removecargarage');
    Route::get('/book/car/garage/{id}', [UserController::class, 'bookcar'])->name('bookcar');


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


//Administrator Routes
Route::prefix('admin')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'AdminDashboard'])->name('admindashboard');
    // Route::resource('/location', LocationController::class); //mp4
    Route::view('/location', 'location.index')->name('location.index');
    // Cars
    Route::prefix('car')->group(function () {
        Route::resource('manufacturers', ManufacturerController::class);
        Route::post('manufacturer/storeMedia', [ManufacturerController::class, 'storeMedia'])->name('manufacturer.storeMedia');
        Route::resource('types', TypeController::class);
        Route::resource('model', ModelController::class);
        Route::resource('fuel', FuelController::class);
        Route::resource('transmission', TransmissionController::class);
        Route::resource('accessories', AccessoriesController::class); //mp3
        Route::post('accessories/import', [AccessoriesController::class, 'import'])->name('accessories.import');
        Route::resource('drivers', DriverController::class); //mp2
        Route::post('drivers/images', [DriverController::class, 'storeMedia'])->name('drivers.storeMedia');
        Route::post('drivers/import', [DriverController::class, 'import'])->name('drivers.import');

        Route::get('/list', [CarController::class, 'index'])->name('car.index'); //mp1
        Route::get('/list/create', [CarController::class, 'create'])->name('car.create');
        Route::post('/store', [CarController::class, 'store'])->name('car.store');
        Route::get('/edit/{id}', [CarController::class, 'edit'])->name('car.edit');
        Route::put('/update/{id}', [CarController::class, 'update'])->name('car.update');
        Route::delete('/delete/{id}', [CarController::class, 'destroy'])->name('car.delete');
        Route::get('/show/{id}', [CarController::class, 'show'])->name('car.show');
    });

    Route::middleware('blockuser')->group(function () {
        Route::get('/bookings', [BookingController::class, 'index'])->name('bookings.index');
        Route::get('/pendings', [BookingController::class, 'adminPendings'])->name('adminPendings');
        Route::get('/confirms', [BookingController::class, 'adminConfirms'])->name('adminConfirms');
        Route::get('/finished', [BookingController::class, 'adminFinish'])->name('adminFinish');

        Route::get('/bookings/create', [AdminController::class, 'createBooking'])->name('createBooking');
        Route::get('/confirm/{id?}', [AdminController::class, 'confirmBooking'])->name('confirmBooking');
        Route::post('/bookings/store', [AdminController::class, 'storeBooking'])->name('storeBooking');
        Route::get('/bookings/{id}/edit', [AdminController::class, 'editBooking'])->name('editBooking');
        Route::put('/bookings/{id}/update', [AdminController::class, 'updateBooking'])->name('updateBooking');
        Route::delete('/bookings/{id}/delete', [AdminController::class, 'deleteBooking'])->name('deleteBooking');
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
