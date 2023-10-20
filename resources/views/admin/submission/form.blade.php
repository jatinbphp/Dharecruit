{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Name :<span class="text-red">*</span></label>
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Name', 'id' => 'name']) !!}
            @if ($errors->has('name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="control-label" for="email">Email :<span class="text-red">*</span></label>
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'email']) !!}
            @if ($errors->has('email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('location') ? ' has-error' : '' }}">
            <label class="control-label" for="location">Location :<span class="text-red">*</span></label>
            {!! Form::text('location', null, ['class' => 'form-control', 'placeholder' => 'Enter Location', 'id' => 'location']) !!}
            @if ($errors->has('location'))
                <span class="text-danger">
                    <strong>{{ $errors->first('location') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">Phone :<span class="text-red">*</span></label>
            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Enter Phone', 'id' => 'phone']) !!}
            @if ($errors->has('phone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('phone') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('employer_detail') ? ' has-error' : '' }}">
            <label class="control-label" for="employer_detail">Employer Detail :<span class="text-red">*</span></label>
            {!! Form::select('employer_detail', \App\Models\Submission::$empDetails, null, ['class' => 'form-control select2','id'=>'employer_detail']) !!}
            @if ($errors->has('employer_detail'))
                <span class="text-danger">
                    <strong>{{ $errors->first('employer_detail') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('work_authorization') ? ' has-error' : '' }}">
            <label class="control-label" for="work_authorization">Work Authorization :<span class="text-red">*</span></label>
            {!! Form::text('work_authorization', null, ['class' => 'form-control', 'placeholder' => 'Enter Work Authorization', 'id' => 'work_authorization']) !!}
            @if ($errors->has('work_authorization'))
                <span class="text-danger">
                    <strong>{{ $errors->first('work_authorization') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('last_4_ssn') ? ' has-error' : '' }}">
            <label class="control-label" for="last_4_ssn">Last 4 SSN :<span class="text-red">*</span></label>
            {!! Form::text('last_4_ssn', null, ['class' => 'form-control', 'placeholder' => 'Enter Last 4 SSN', 'id' => 'last_4_ssn']) !!}
            @if ($errors->has('last_4_ssn'))
                <span class="text-danger">
                    <strong>{{ $errors->first('last_4_ssn') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('education_details') ? ' has-error' : '' }}">
            <label class="control-label" for="education_details">Education Details :<span class="text-red">*</span></label>
            {!! Form::text('education_details', null, ['class' => 'form-control', 'placeholder' => 'Enter Education Details', 'id' => 'education_details']) !!}
            @if ($errors->has('education_details'))
                <span class="text-danger">
                    <strong>{{ $errors->first('education_details') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('resume_experience') ? ' has-error' : '' }}">
            <label class="control-label" for="resume_experience">Resume Experience :<span class="text-red">*</span></label>
            {!! Form::text('resume_experience', null, ['class' => 'form-control', 'placeholder' => 'Enter Resume Experience', 'id' => 'resume_experience']) !!}
            @if ($errors->has('resume_experience'))
                <span class="text-danger">
                    <strong>{{ $errors->first('resume_experience') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('linkedin_id') ? ' has-error' : '' }}">
            <label class="control-label" for="linkedin_id">Linkedin ID :<span class="text-red">*</span></label>
            {!! Form::text('linkedin_id', null, ['class' => 'form-control', 'placeholder' => 'Enter Linkedin ID', 'id' => 'linkedin_id']) !!}
            @if ($errors->has('linkedin_id'))
                <span class="text-danger">
                    <strong>{{ $errors->first('linkedin_id') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('relocation') ? ' has-error' : '' }}">
            <label class="control-label" for="relocation">Relocation :<span class="text-red">*</span></label>
            {!! Form::text('relocation', null, ['class' => 'form-control', 'placeholder' => 'Enter Relocation', 'id' => 'relocation']) !!}
            @if ($errors->has('relocation'))
                <span class="text-danger">
                    <strong>{{ $errors->first('relocation') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('vendor_rate') ? ' has-error' : '' }}">
            <label class="control-label" for="vendor_rate">Vendor Rate :<span class="text-red">*</span></label>
            {!! Form::text('vendor_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter Vendor Rate', 'id' => 'vendor_rate']) !!}
            @if ($errors->has('vendor_rate'))
                <span class="text-danger">
                    <strong>{{ $errors->first('vendor_rate') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('resume') ? ' has-error' : '' }}">
            <label class="control-label" for="resume">Resume :<span class="text-red">*</span></label>
            {!! Form::file('resume', ['class' => '', 'id'=> 'resume','accept'=>'.xlsx,.xls,.doc,.docx,.ppt,.pptx,.txt,.pdf']) !!}
            {!! Form::hidden('existResume', null, ['id' => 'existResume']) !!}
            @if ($errors->has('resume'))
            <br>
            <span class="text-danger">
                    <strong>{{ $errors->first('resume') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
@if(!isset($submission))
    <div class="text-right">
        <button class="btn btn-info" id="empSave">Save</button>
    </div>
@endif

<div id="empDiv" @if(!isset($submission)) style="display: none;" @endif>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('employer_name') ? ' has-error' : '' }}">
                <label class="control-label" for="employer_name">Employer Name :<span class="text-red">*</span></label>
                {!! Form::text('employer_name', null, ['class' => 'form-control','placeholder' => 'Enter Employer Name', 'id'=>'employer_name']) !!}
                @if ($errors->has('employer_name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('employer_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('employee_name') ? ' has-error' : '' }}">
                <label class="control-label" for="employee_name">Employee Name :<span class="text-red">*</span></label>
                {!! Form::text('employee_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Name', 'id' => 'employee_name']) !!}
                @if ($errors->has('employee_name'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('employee_name') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group{{ $errors->has('employee_email') ? ' has-error' : '' }}">
                <label class="control-label" for="employee_email">Employee Email :<span class="text-red">*</span></label>
                {!! Form::text('employee_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Email', 'id' => 'employee_email']) !!}
                @if ($errors->has('employee_email'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('employee_email') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group{{ $errors->has('employee_phone') ? ' has-error' : '' }}">
                <label class="control-label" for="employee_phone">Employee Phone Number :<span class="text-red">*</span></label>
                {!! Form::text('employee_phone', null, ['class' => 'form-control', 'placeholder' => 'Employee Phone Number', 'id' => 'employee_phone']) !!}
                @if ($errors->has('employee_phone'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('employee_phone') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="text-right">
        <button class="btn btn-info" id="empSave">Save</button>
    </div>
</div>