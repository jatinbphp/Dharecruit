<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MailTemplate;
use Illuminate\Http\Request;

class MailTemplateController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
        $this->middleware('accessright:manage_mail_template');
    }

    public function index()
    {
        $data['templateData'] = MailTemplate::get();
        $data['menu'] = 'Mail Template';

        return view('admin.mail_template.templates', $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $data['templateData'] = MailTemplate::where('id',$id)->first();
        return view('admin.mail_template.edit_template', $data)->render();
    }

    public function update(Request $request, $id)
    {
        if(empty($request->subject) || empty($request->content)){
            return 0;
        }
        $template = MailTemplate::findOrFail($id);
        $template->update($request->all());

        return 1;
    }

    public function destroy($id)
    {
        //
    }
}
