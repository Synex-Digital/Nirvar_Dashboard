<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class AdminLogoutController extends Controller
{
    public function logout()
    {

        Auth::guard('admin')->logout();
        return redirect('sd_admin/login');
    }
}
