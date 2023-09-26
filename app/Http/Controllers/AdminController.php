<?php

namespace App\Http\Controllers;

use App\RequestData;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;


class AdminController extends Controller
{
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
        return view('home');
    }

    public function create_user()
    {
        return view('create_user');
    }

    public function store_user(Request $request)
    {
        $data = $request->all();

        $rules = [
            'name' => "required",
            'contact_number' => "required",
            'email' => "required|email|unique:users,email",
            'password' => "required|confirmed|min:8",
            'role' => "required",
        ];
        $message = [
            'name' => 'The Name must be required',
            'contact_number' => 'The Contact Number must be required',
            'email' => 'The Email must be required',
            'password' => 'The Password must be required',
            'role' => 'The Gender must be required',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator)
                ->with('message_type', 'danger')
                ->with('message', 'There were some error try again');
        }        
        User::Insert([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'contact_number' => $data['contact_number'],
            'role' => $data['role'],
            'status' => 0,
        ]);
        return redirect()->back()->with('success', 'User Create Successfully.');
    }

    public function users()
    {
        return view('users');
    }

    public function get_users(Request $request)
    {
        $totalFilteredRecord = $totalDataRecord = $draw_val = "";
        $columns_list        = [
            0 => 'id',
            1 => 'name',
            2 => 'email',
            3 => 'contact_number',
            4 => 'password',
            5 => 'role',
            6 => 'status',
            7 => 'id',            
        ];

        $totalDataRecord = User::where('role','!=','admin')->count();

        $totalFilteredRecord = $totalDataRecord;

        $limit_val = $request->input('length');
        $start_val = $request->input('start');
        $order_val = $columns_list[$request->input('order.0.column')];
        $dir_val   = $request->input('order.0.dir');

        if (empty($request->input('search.value'))) {
            $post_data = User::where('role','!=','admin')->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
        } else {
            $search_text = $request->input('search.value');

            $post_data = User::where('role','!=','admin')->where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('name', 'LIKE', "%{$search_text}%")
                ->orWhere('role', 'LIKE', "%{$search_text}%")
                ->orWhere('contact_number', 'LIKE', "%{$search_text}%")
                ->orWhere('email', 'LIKE', "%{$search_text}%")
                ->offset($start_val)
                ->limit($limit_val)
                ->orderBy($order_val, $dir_val)
                ->get();
            $totalFilteredRecord = User::where('role','!=','admin')->where('id', 'LIKE', "%{$search_text}%")
                ->orWhere('name', 'LIKE', "%{$search_text}%")
                ->orWhere('role', 'LIKE', "%{$search_text}%")
                ->orWhere('contact_number', 'LIKE', "%{$search_text}%")
                ->orWhere('email', 'LIKE', "%{$search_text}%")
                ->count();
        }

        $data_val = [];
        if (!empty($post_data)) {
            foreach ($post_data as $key=>$post_val) {
                if ($post_val->status == 0) {
                    $status = '<input type="checkbox" class="statusChanged" name="status" value="' . $post_val->id . '" data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                } else {
                    $status = '<input type="checkbox" class="statusChanged" name="status" value="' . $post_val->id . '" checked data-bootstrap-switch data-off-color="danger" data-on-color="success">';
                }

                $postnestedData['DT_RowId']           = $post_val->id;
                $postnestedData['id']                 = $key+1;
                $postnestedData['name']               = $post_val->name;
                $postnestedData['email']              = $post_val->email;
                $postnestedData['contact_number']     = $post_val->contact_number;
                $postnestedData['role']               = $post_val->role;
                $postnestedData['status']             = $status;
                $url                                  = url('admin/edit-user/' . $post_val->id);
                $postnestedData['options']            = "<a href='" . $url . "' title='Edit' class='btn btn-info btn-sm'><i class='fas fa-edit'></i></a> <a title='Delete' href='javascript:void(0)' class='btn btn-danger btn-sm' onClick='removeUser(" . $post_val->id . ")'><i class='fas fa-trash'></i></a>";
                $data_val[]                 = $postnestedData;

            }
        }
        $draw_val      = $request->input('draw');
        $get_json_data = [
            "draw"            => intval($draw_val),
            "recordsTotal"    => intval($totalDataRecord),
            "recordsFiltered" => intval($totalFilteredRecord),
            "data"            => $data_val,
        ];

        echo json_encode($get_json_data);
    }

    public function destroyUser(Request $request)
    {        
        $post = User::where('id',$request->id)->first();
        $post->delete();
    }

    public function statusUser(Request $request)
    {
        $updateRequest = $request->all();
        $data          = [
            'status' => $updateRequest['status'],
        ];
        User::where('id', $updateRequest['id'])->update($data);
    }

    public function edit_user($id)
    {
        $users = User::find($id);
        return view('edit_user',compact('users'));
    }

    public function update_user(Request $request)
    {
        $data = $request->all();        
        $rules = [
            'name' => "required",
            'contact_number' => "required",
            'email' => "required|email|unique:users,email,".$data['userId'],
            'password' => "nullable|confirmed|min:8",
            'role' => "required",
        ];
        $message = [
            'name' => 'The Name must be required',
            'contact_number' => 'The Contact Number must be required',
            'email' => 'The Email must be required',
            'password' => 'The Password must be required',
            'role' => 'The Gender must be required',
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator)
                ->with('message_type', 'danger')
                ->with('message', 'There were some error try again');
        }        
        $Update = [
            'name' => $data['name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'role' => $data['role'],
        ];
        User::where('id',$data['userId'])->update($Update);

        if($data['password'] != '') {
            $password = ['password' => Hash::make($data['password'])];
            User::where('id',$data['userId'])->update($password);
        }
        return redirect()->back()->with('success', 'User Update Successfully.');
    }

    public function change_password()
    {
        return view('changepassword');
    }

    public function update_password(Request $request)
    {
        if ($request->password != '') {
            User::whereId(auth()->user()->id)->update([
                'password' => Hash::make($request->password),
            ]);
            return back()->with('success', 'Password Update Successfully');
        } else {
            return back()->with('errorM', 'Please enter your new password');
        }
    }

}
