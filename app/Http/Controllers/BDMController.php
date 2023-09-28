<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requirements;
use App\PV;
use Illuminate\Support\Facades\Validator;
class BDMController extends Controller
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
        return view('my_requirement');
    }

    public function create_requirement()
    {
        return view('create_requirement');
    }

    public function store_requirements(Request $request)
    {
        $data  = $request->all();
        $rules = [
            'job_title' => "required",
            'no_position' => "required",
            'experience' => "required",
            'locations' => "required",
            'work_type' => "required",
            'duration' => "required",
            'visa' => "required",
            'client' => "required",
            'vendor_rate' => "required",
            'my_rate' => "required",
            'priority' => "required",
            'term' => "required",
            'category' => "required",
            'MOI' => "required",
            'job_keyword' => "required",
            'job_description' => "required",
        ];
        $message = [
            'job_title' => "The Job Title must be required",
            'no_position' => "The No Position must be required",
            'experience' => "The Experience must be required",
            'locations' => "The Locations must be required",
            'work_type' => "The Work Type must be required",
            'duration' => "The Duration must be required",
            'visa' => "The Visa must be required",
            'client' => "The client must be required",
            'vendor_rate' => "The Vendor_rate must be required",
            'my_rate' => "The My Rate must be required",
            'priority' => "The Priority must be required",
            'term' => "The Term must be required",
            'category' => "The Category must be required",
            'MOI' => "The MOI must be required",
            'job_keyword' => "The Job Keyword must be required",
            'job_description' => "The Job Description must be required",
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator)
                ->with('message_type', 'danger')
                ->with('message', 'There were some error try again');
        }

        $requirementId = Requirements::insertGetId([
            'job_title'                 => $request->job_title,
            'no_position'               => $request->no_position,
            'experience'                => $request->experience,
            'locations'                 => $request->locations,
            'work_type'                 => $request->work_type,
            'duration'                  => $request->duration,
            'visa'                      => $request->visa,
            'client'                    => $request->client,
            'vendor_rate'               => $request->vendor_rate,
            'my_rate'                   => $request->my_rate,
            'priority'                  => $request->priority,
            'term'                      => $request->term,
            'category'                  => $request->category,
            'MOI'                       => $request->MOI,
            'job_keyword'               => $request->job_keyword,
            'special_notes'             => $request->special_notes,
            'job_description'           => $request->job_description,
            'bdm_id'                    => auth()->user()->id,
            "created_at"                 => date("Y-m-d H:i:s"),
            "updated_at"                 => date("Y-m-d H:i:s"),
        ]);
        return redirect()->back()->with('success', 'Requirement Create Successfully.');
    }


    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function pv()
    {        
        return view('pv');
    }

    public function create_pv()
    {
        return view('create_pv');
    }

    public function store_pv(Request $request)
    {
        $data  = $request->all();
        $rules = [
            'company_name' => "required",
            'poc_name' => "required",
            'poc_email' => "required|email",
            'poc_phone' => "required",
            'client_name' => "required",
        ];
        $message = [
            'company_name' => "The Job Title must be required",
            'poc_name' => "The No Position must be required",
            'poc_email' => "The Experience must be required",
            'poc_phone' => "The Locations must be required",
            'client_name' => "The Visa must be required",
        ];
        $validator = Validator::make($data, $rules, $message);
        if ($validator->fails()) {
            return back()->withInput()
                ->withErrors($validator)
                ->with('message_type', 'danger')
                ->with('message', 'There were some error try again');
        }


        $requirementId = PV::insertGetId([
            'company_name'                  => $request->company_name,
            'poc_name'                      => $request->poc_name,
            'poc_email'                     => $request->poc_email,
            'poc_phone'                     => $request->poc_phone,
            'poc_location'                  => $request->poc_location,
            'pv_company_location'           => $request->pv_company_location,
            'client_name'                   => $request->client_name,
            'check_display_client'          => isset($request->check_display_client) ? 1 : 0,
            'bdm_id'                        => auth()->user()->id,
            "created_at"                    => date("Y-m-d H:i:s"),
            "updated_at"                    => date("Y-m-d H:i:s"),
        ]);
        return redirect()->back()->with('success', 'PV Data Successfully.');
    }
}
