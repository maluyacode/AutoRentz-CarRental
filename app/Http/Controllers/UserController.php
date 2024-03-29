<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Customer;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Mail;
use App\Models\Booking;
use App\CustomerClass;
use App\Mail\Booking as MailBooking;
use App\Models\Accessorie;
use App\Models\Car;
use App\Models\Location;
use App\DataTables\UserDataTable;
use League\Flysystem\Adapter\Local;
use App\Events\UserBookEvent;
use App\Models\Driver;
use Barryvdh\Debugbar\Facades\Debugbar;

class UserController extends Controller
{
    public function profile() // viewing profile of a user/customer
    {
        $customer = Customer::where('user_id', Auth::user()->id)->first();

        if (!$customer) {
            $customer = new CustomerClass;
            $customer->insert();
        }

        $user = User::with(['customer'])->find(Auth::user()->id);

        return View::make('user.profile', compact('user'));
    }

    public function changePassword(Request $request, $id) // specific for changing a password of a user
    {
        try {
            $user = User::findOrFail($id);
            if (!password_verify($request->prevpass, $user->password)) {
                return back()->with('warning', 'Wrong previous password, try to reset it');
            }
            if ($request->newpass != $request->confirmpass) {
                return back()->with('warning', "Password confirmation doesn't match");
            }
            $user->password = Hash::make($request->newpass);
            $user->save();
            return back()->with('update', 'Password successfuly Change');
        } catch (\Exception $e) {
            return back()->with('warning', 'User not exist!');
        }
    }

    public function edit(Request $request, $id) // editing user/customer details
    {
        $user = Customer::find($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->email = $request->email;

        $userTable = User::find($user->user_id);
        $userTable->name = $request->name;
        $userTable->email = $request->email;

        if ($request->file()) { // uploading a file
            $fileName = time() . '_' . $request->file('image_path')->getClientOriginalName();

            $path = Storage::putFileAs('public/images', $request->file('image_path'), $fileName);
            $user->image_path = '/storage/images/' . $fileName;
        }
        $userTable->save();
        $user->save();
        return back()->with("update", "Updated Successfully!");
    }

    public function addtousergarage($id)
    {
        $user = Customer::where('user_id', auth()->user()->id)->first();
        if (!$user) {
            return redirect()->route('viewprofile')->with('warning', 'Please complete your profile information.');
        }
        $car = Car::find($id);
        if ($car->car_status == 'taken') {
            return back()->with("warning", "I'm sorry but this car is already taken");
        }
        $user = Auth::user()->id;
        $customer_id = Auth::user()->customer->id;
        $garage = Session::has('garage' . $user) ? Session::get('garage' . $user) : null;

        $userGarage = new CustomerClass();
        // calling the method of customerclass to add a new car to his/garage
        $fromclass = $userGarage->addToUserGarage($garage, $id, $customer_id);

        if ($fromclass == 'created') {
            return redirect()->route('viewusergarage')->with("created", "You have already created your garage");
        } else if ($fromclass == 'inserted') {
            return back()->with("inserted", "Car added! kindly check your garage for editing the details.");
        } else {
            return back()->with("already", "I'm sorry but this car is already in your garage");
        }
    }

    public function viewusergarage()
    {
        $garage = Session::get('garage' . Auth::user()->id);


        $locations = Location::select(DB::raw("CONCAT(street, ', ', baranggay, ', ', city) AS lugar"), 'id')->get()->pluck('lugar', 'id')->toArray();

        if (!$garage) {
            return View::make('user.garage', ["carInGarage" => null, "locations" => $locations]);
        }


        $cars = Car::with([
            'modelo',
            'modelo.manufacturer',
            'modelo.type',
            'transmission',
            'fuel',
            'accessories',
            'media',
        ])->get()->keyBy('id');



        foreach ($cars as $car) {
            if (array_key_exists($car->id, $garage)) {
                $garage[$car->id]["car"] = $car;

                $accessoriesFee = $car->accessories->map(function ($accessory) {
                    return $accessory->fee;
                })->sum();

                $garage[$car->id]["totalPrice"] = $car->price_per_day + $accessoriesFee;
            }
        }

        $carInGarage = null;
        foreach ($garage as $key => $garageData) {

            $locationsForGarage = [];

            if (array_key_exists($garageData["pick_id"], $locations)) {
                $locationsForGarage["pick"] = $locations[$garageData["pick_id"]];
            }

            if (array_key_exists($garageData["return_id"], $locations)) {
                $locationsForGarage["return"] = $locations[$garageData["return_id"]];
            }

            $diff = date_diff(date_create($garageData['start_date']), date_create($garageData['end_date']));
            $count = (int) $diff->format('%a');
            $garageData["days"] = $count + 1;

            $garageData["locations"] = $locationsForGarage;
            $carInGarage[$key] = $garageData;
        }

        return View::make('user.garage', compact('carInGarage', 'locations'));
    }

    public function removecargarage($id)
    {
        $garage = Session::get('garage' . Auth::user()->id);
        if ($garage && $id) {
            unset($garage[$id]);
            Session::put('garage' . Auth::user()->id, $garage);
            Session::save();
        }
        if ($garage) {
            return redirect()->route('viewusergarage')->with("deleted", "Car already removed to your garage!");
        } else {
            return redirect()->route('viewusergarage')->with("deleted", "Your garage is empty!");
        }
    }

    public function bookcar(Request $request)
    {

        $user = Customer::where('user_id', auth()->user()->id)->first();

        if (!($user->address && $user->phone)) {
            return redirect()->route('viewprofile')->with('warning', 'Please complete your profile information.');
        }

        $usergarage = Session::get('garage' . auth()->user()->id);

        $customerClass = new CustomerClass();
        $newbook = $customerClass->sessionToBooking($usergarage, $request);

        Debugbar::info($newbook);

        $user = User::find(auth()->user()->id);

        try {
            UserBookEvent::dispatch($newbook, $user->email, $user->name);
        } catch (\Exception $e) {
            Debugbar::info($e);
            return redirect()->route('viewusergarage')->with('success', 'Reservation confirmed, without notify email to admin, due to connection problem');
        }

        return response()->json($newbook);
        // return redirect()->route('editgarage', $id)->with('update', 'Please specify required details.');
    }


    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('user.index');
    }

    public function createUser()
    {
        return view('user.create');
    }

    public function storeUser(Request $request)
    {
        Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2',
                'phone' => 'required|numeric|min:11',
                'address' => 'required|min:4|max:100',
                'image_path' => 'required|mimes:jpg,png',
                'email' => 'required|email|unique:users,email',
                'pass' => 'required|min:4|max:12',
                'role' => 'required',
            ]
        )->validate();
        $data = ['name' => $request->name, 'email' => $request->email, 'password' => $request->pass];
        $registerUser = new RegisterController();
        $registerUser->create($data);

        $user = User::find(User::max('id'));
        User::find($user->id)->update([
            'role' => $request->role,
        ]);
        $customer = new Customer;
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->address = $request->address;
        $customer->phone = $request->phone;
        $customer->user_id = $user->id;
        if ($request->file()) {
            $fileName = time() . '_' . $request->file('image_path')->getClientOriginalName();

            $path = Storage::putFileAs('public/images', $request->file('image_path'), $fileName);
            $customer->image_path = '/storage/images/' . $fileName;
        }
        $customer->save();
        return redirect()->route('users.index')->with('created', 'New ' . $request->role . ' created');
    }

    public function editUser($id)
    {
        $user = DB::table('users as us')
            ->select('cu.*', 'us.role as role', 'us.id as user_id', 'us.email as user_email')
            ->join('customers as cu', 'us.id', 'cu.user_id')
            ->where('us.id', $id)->first();
        return view('user.edit', compact('user'));
    }

    public function updateUser(Request $request, $id)
    {
        Validator::make(
            $request->all(),
            [
                'name' => 'required|min:2',
                'phone' => 'required|numeric|min:11',
                'address' => 'required|min:4|max:100',
                'email' => 'required|email',
                'role' => 'required',
            ]
        )->validate();
        $userTable = User::find($id);
        $userTable->name = $request->name;
        $userTable->email = $request->email;
        $userTable->role = $request->role;
        if ($request->pass) {
            $userTable->password = Hash::make($request->pass);
        }
        $userTable->save();
        $customerTable = Customer::where('user_id', $id)->first();
        $customerTable->name = $request->name;
        $customerTable->email = $request->email;
        $customerTable->phone = $request->phone;
        $customerTable->address = $request->address;
        if ($request->file()) {
            $fileName = time() . '_' . $request->file('image_path')->getClientOriginalName();

            $path = Storage::putFileAs('public/images', $request->file('image_path'), $fileName);
            $customerTable->image_path = '/storage/images/' . $fileName;
        }
        $customerTable->save();
        return redirect()->route('users.index')->with('updates', 'Successfully updated');
    }

    public function destroyUser($id)
    {
        User::destroy($id);
        return redirect()->route('users.index')->with('deleted', 'Successfully deleted');
    }
}
