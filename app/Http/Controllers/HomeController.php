<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Role;
use App\Models\User;


class HomeController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $id = Auth::user()->id;
        $role = User::join('role_user', 'users.id', '=', 'role_user.user_id')
            ->join('roles', 'role_user.role_id', '=', 'roles.id')
            ->select('roles.name')
            ->where('users.id', '=', $id)
            ->get()->first();
        // dd($role->name);
        if ($role->name == 'Administrator') {
            return redirect()->route('admindashboard');
        } elseif ($role->name == 'Processor') {
            return redirect()->route('processordashboard');
        } elseif ($role->name == 'Validator') {
            return redirect()->route('validatordashboard');
        } elseif ($role->name == 'Approver') {
            return redirect()->route('approverdashboard');
        } elseif ($role->name == 'Requestor') {
            return redirect()->route('requestordashboard');
        }

        // if ($id === "ASSD Manager") {
        //     return redirect()->route('approverdashboard');
        // } elseif ($id === "Corporate Treasurer") {
        //     return redirect()->route('approverdashboard');
        // } elseif ($id === "Chief Executive Officer") {
        //     return redirect()->route('approverdashboard');
        // } elseif ($id === "Department Head") {
        //     return redirect()->route('requestordashboard');
        // } elseif ($id === "Finance Head") {
        //     return redirect()->route('validatordashboard');
        // } elseif ($id === "Administrator") {
        //     return redirect()->route('admindashboard');
        // } elseif ($id === "Procurement Officer") {
        //     return redirect()->route('processordashboard');
        // } else {
        //     return redirect('pet/index');
        // }
    }
}
