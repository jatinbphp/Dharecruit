<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SettingController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_settings');
    }

    public function index()
    {
        $data['menu'] = "Manage Settings";
        $data['settingData'] = Setting::pluck('value','name')->toArray();
        $data['yesNoOptions'] = [
            'yes' => 'Yes',
            'no' => 'No'
        ];
        return view('admin.setting.setting_form', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $requestData = $request->except(['_token']);
        $storedData = Setting::pluck('name')->toArray();

        foreach ($requestData as $key => $value) {
            if(in_array($key,$storedData)){
                $settingData = Setting::where('name', $key)->first();

                if(!empty($settingData)){
                    $settingData->value = $value;
                    $settingData->save();
                }
            } else {
                Setting::create(['name' => $key, 'value' => $value]);
            }
        }

        return redirect()->route('setting.index');
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
