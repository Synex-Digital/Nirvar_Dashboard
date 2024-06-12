<?php

namespace App\Http\Controllers\admin;

use App\Models\Admin;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminLoginController extends Controller
{
    public function login()
    {
        return view('dashboard.admin.auth.login');
    }
    public function loginAdminForm(Request $request){

        $request->validate([
            'email' => 'required',
            'password' => 'required',
        ]);
        $admin = Admin::where('email', $request->email)->first();
        if ($admin ) {
            if(Hash::check($request->password, $admin->password)){

                Auth::guard('admin')->login($admin);
               return redirect()->route('admin.index');
            }else{
                return back()->with('password', 'Invalid password.');
            }
        } else {
            return back()->with('email', 'Invalid email address.');

        }

    }
}
