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

class UserController extends Controller
{
    public function profile() // viewing profile of a user/customer
    {
        $customer = Customer::where('user_id', Auth::user()->id)->first();
        if (!$customer) {
            $customer = new CustomerClass;
            $customer->insert();
        }
        $user = Customer::where('user_id', Auth::user()->id)->first();
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

        $carDetails = new CustomerClass();
        $garage = Session::get('garage' . Auth::user()->id);
        $location = new Location;
        $accessory = DB::table('cars as ca')
            ->join('accessorie_car as ac_ca', 'ca.id', 'ac_ca.car_id')
            ->join('accessories as ac', 'ac_ca.accessorie_id', 'ac.id')
            ->get();
        // dd($accessory);
        return View::make('user.garage', compact('garage', 'carDetails', 'location', 'accessory'));
    }

    public function editgarage($id)
    {
        $carInGarage = Session::get('garage' . Auth::user()->id)[$id];
        $car = new CustomerClass();

        $carAccessories = Car::find($carInGarage['car_id'])->accessories()->get();

        $location = new Location;

        if ($carInGarage['pick_id'] && $carInGarage['return_id']) {
            $pickLocation = Location::whereNotIn('id', [$carInGarage['pick_id']])->get();
            $returnLocation = Location::whereNotIn('id', [$carInGarage['return_id']])->get();
            // dd($pickLocation);
        } else {
            $pickLocation = Location::all();
            $returnLocation = Location::all();
        }

        // dd($name->street);
        // dd($bookcar['car_id']);
        return View::make('user.edit-garage-car', compact('carInGarage', 'car', 'carAccessories', 'location', 'pickLocation', 'returnLocation'));
    }

    public function savegarage(Request $request, $id)
    {
        $customerClass = new CustomerClass;
        $transactionType = $customerClass->CheckTypeOfTransaction($request->typeget, $request->return_id, $request->pick_id, $request->address);
        $editedInfo = ['customer_id' => $request->customer_id, 'car_id' => $id, 'start_date' => $request->start_date, 'end_date' => $request->end_date, 'pick_id' => $transactionType['pick_id'], 'return_id' => $transactionType['return_id'], 'address' => $transactionType['address'], 'driver_id' => $request->drivetype, 'status' => 'pending'];
        $updatedInfo =  $customerClass->saveToGarageSession($editedInfo);
        return back()->with("update", "Updated Successfully!");
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
            return redirect()->route('viewusergarage')->with("deleted", "Car already removed to your garage!👉👈");
        } else {
            return redirect()->route('viewusergarage')->with("deleted", "Your garage is empty!");
        }
    }

    public function bookcar($id)
    {
        $user = Customer::where('user_id', auth()->user()->id)->first();
        if (!($user->address && $user->phone)) {
            return redirect()->route('viewprofile')->with('warning', 'Please complete your profile information.');
        }
        $usergarage = Session::get('garage' . auth()->user()->id);
        if ((!$usergarage[$id]['start_date'] == null && !$usergarage[$id]['end_date'] == null) && (($usergarage[$id]['address']) || ($usergarage[$id]['pick_id'] && $usergarage[$id]['return_id']))) {
            $customerClass = new CustomerClass();
            $message = $customerClass->sessionToBooking($usergarage, $id);

            $user = User::find(auth()->user()->id);
            try {
                $mail = new MailBooking();
                $mailmessage = $mail->build();
                $mailmessage->from($user->email, $user->name);
                Mail::to('autorentz24@gmail.com')->send($mailmessage);
            } catch (\Exception $e) {
                return redirect()->route('viewusergarage')->with('success', 'Reservation confirmed, without email, due to connection problem');
            }
            return redirect()->route('viewusergarage')->with('success', "Reservation Confirmed! Your car has been successfully reserved. Please check your booking management for further details.");
        }
        return redirect()->route('editgarage', $id)->with('update', 'Please specify required details.');
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
