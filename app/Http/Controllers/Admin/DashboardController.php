<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $data['menu'] = 'Dashboard';
        $data['loggedUser'] = $this->getUser();
        $data['users'] = Admin::where('id', '!=', Auth::user()->id)->where('role','admin')->count();
        return view('admin.dashboard',$data);
    }
}
