<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use DataTables;


class CategoryController extends Controller
{
    public function __construct(Request $request){
        $this->middleware('auth');
    }

    public function index(Request $request){
        $data['menu']="Categories";


         if ($request->ajax()) {
            $data = Category::get();
            return Datatables::of($data)
                    ->addIndexColumn()
                    
                    ->addColumn('action', function($row){
                        $btn = '<div class="btn-group btn-group-sm"><a href="'.url('admin/category/'.$row->id.'/edit').'"><button class="btn btn-sm btn-info tip" data-toggle="tooltip" title="Edit Stock" data-trigger="hover" type="submit" ><i class="fa fa-edit"></i></button></a></div>';
                        $btn .= '<span data-toggle="tooltip" title="Delete Stock" data-trigger="hover">
                                    <button class="btn btn-sm btn-danger delete_category" data-id="'.$row->id.'" type="button"><i class="fa fa-trash"></i></button>
                                </span>';
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
        }

        return view('category.index', $data);
    }

    public function create(){
        $data['menu']="Categories";
        return view('category.create',$data);
    }

    public function store(Request $request){
        $this->validate($request, [
            'name' => 'required',
        ]);

        $input = $request->all();
        $category = Category::create($input);
        \Session::flash('success', 'Category has been inserted successfully!');
        return redirect()->route('category.index');
    }

    public function show(string $id){
        //
    }

    public function edit(string $id){
        $data['menu']="Categories";
        $data['categories'] = Category::findorFail($id);
        return view('category.edit',$data);
    }

    public function update(Request $request, $id){
        $this->validate($request, [
            'name' => 'required',
        ]);

        $input = $request->all();
        $category = Category::findorFail($id);
        $category->update($input);

        \Session::flash('success','category has been updated successfully!');
         return redirect()->route('category.index');
    }

    public function destroy($id){
        $category = Category::findOrFail($id);
        if(!empty($category)){
            $category->delete();
            return response()->json('success', 200);
        }else{
           return response()->json('fail', 500);
        }
    }
}
