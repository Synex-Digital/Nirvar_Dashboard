<?php

namespace App\Http\Controllers\admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class AdminRegisterController extends Controller
{
    public function register()
    {
        return view('dashboard.admin.auth.register');
    }
    public function register_store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'password' => 'required| min:8| confirmed',
            'password_confirmation' => 'required| min:8',
        ]);
        $admin = new Admin;
        $admin->name = $request->name;
        $admin->email = $request->email;
        $admin->password = Hash::make($request->password);
        $admin->save();
        return redirect()->route('adminLogin');
    }
}
