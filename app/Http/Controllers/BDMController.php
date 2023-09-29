<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Requirements;
use App\PV;
use Illuminate\Support\Facades\Validator;
use DataTables;
use Illuminate\Support\Facades\Session;
use App\Models\Category;
use App\Models\Moi;



class BDMController extends Controller{
    
    public function __construct(){
        $this->middleware('auth');
    }

    public function index(Request $request){
        $data['menu']="Requirements";
        if ($request->ajax()){
            $data = Requirements::get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    ->addColumn('action', function($row){
                            $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/edit-requirements/'.$row->id).'"><button class="btn btn-sm btn-info tip mr-1" data-toggle="tooltip" title="Edit Stock" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                            $btn .= '<span data-toggle="tooltip" title="Delete" data-trigger="hover"><button class="btn btn-sm btn-danger delete_requirement" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button></span>';
                        return $btn;
                    })->rawColumns(['action'])->make(true);
        }
        return view('my_requirement', $data);
    }

    public function create_requirement(){
        $data['menu']="Requirements";
        $data['categories'] = Category::all();
        $data['mois'] = Moi::all();
        return view('create_requirement', $data);
    }

    public function store_requirements(Request $request){
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
            'company_name' => "required",
            'poc_name' => "required",
            'poc_email' => "required|email",
            'poc_phone' => "required",
            'client_name' => "required",
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
            'company_name'                  => $request->company_name,
            'poc_name'                      => $request->poc_name,
            'poc_email'                     => $request->poc_email,
            'poc_phone'                     => $request->poc_phone,
            'poc_location'                  => $request->poc_location,
            'pv_company_location'           => $request->pv_company_location,
            'client_name'                   => $request->client_name,
            'check_display_client'          => isset($request->check_display_client) ? 1 : 0,
            'bdm_id'                    => auth()->user()->id,
            "created_at"                 => date("Y-m-d H:i:s"),
            "updated_at"                 => date("Y-m-d H:i:s"),
        ]);
        return redirect()->back()->with('success', 'Requirement Create Successfully.');
    }

    public function edit_requirements($id){
        $data['menu']="Requirements";
        $data['requirements'] = Requirements::findorFail($id);
        $data['categories'] = Category::all();
        $data['mois'] = Moi::all();
        return view('edit_requirements',$data);
    }

    public function update_requirements(Request $request, $id){
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
            'company_name' => "required",
            'poc_name' => "required",
            'poc_email' => "required|email",
            'poc_phone' => "required",
            'client_name' => "required",
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

        $requirements = Requirements::findorFail($id);
        $requirements->update($data);

        Session::flash('success','requirement has been updated successfully!');
        return redirect()->route('admin.my-requirement');
    }

    public function destroy_requirements($id){
        $requirement = Requirements::findOrFail($id);
        if(!empty($requirement)){
            $requirement->delete();
            return response()->json('success', 200);
        }else{
           return response()->json('fail', 404);
        }
    }
}
