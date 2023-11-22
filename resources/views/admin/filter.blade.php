@php
    $bdm = \App\Models\Admin::where('role','bdm')->where('status','active')->pluck('name','id')->prepend('Please Select','');
    $recruiter = \App\Models\Admin::where('role','recruiter')->where('status','active')->pluck('name','id')->prepend('Please Select','');
    $pvCompany = \App\Models\PVCompany::where('status','active')->pluck('name','id')->prepend('Please Select','');
    $moi = \App\Models\Moi::where('status','active')->pluck('name','id')->prepend('Please Select','');
@endphp
<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label" for="date">From Date</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select From Date', 'id' => 'fromDate']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label" for="date">To Date</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">
                        <i class="far fa-calendar-alt"></i>
                    </span>
                </div>
                {!! Form::text('date', null, ['autocomplete' => 'off', 'class' => 'datepicker form-control float-right', 'placeholder' => 'Select To Date', 'id' => 'toDate']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('requirement') ? ' has-error' : '' }}">
            <label class="control-label" for="requirement">Requirement Search</label>
            {!! Form::text('requirement', null, ['class' => 'form-control', 'placeholder' => 'Enter Requirement', 'id' => 'requirement']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('bdm') ? ' has-error' : '' }}">
            <label class="control-label" for="bdm">BDM Search</label>
            {!! Form::select('bdm', $bdm, null, ['class' => 'form-control select2','id'=>'bdm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('recruiter') ? ' has-error' : '' }}">
            <label class="control-label" for="recruiter">Recruiter Search</label>
            {!! Form::select('recruiter', $recruiter, null, ['class' => 'form-control select2','id'=>'recruiter']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('poc_email') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_email">POC Email</label>
            {!! Form::text('poc_email', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'poc_email']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('pv_company') ? ' has-error' : '' }}">
            <label class="control-label" for="pv_company">PV Company</label>
            {!! Form::select('pv_company', $pvCompany, null, ['class' => 'form-control select2','id'=>'pv_company']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('moi') ? ' has-error' : '' }}">
            <label class="control-label" for="moi">MOI</label>
            {!! Form::select('moi', $moi, null, ['class' => 'form-control select2','id'=>'moi']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('work_type') ? ' has-error' : '' }}">
            <label class="control-label" for="work_type">Work Type</label>
            {!! Form::select('work_type', \App\Models\Requirement::$workType, null, ['class' => 'form-control select2','id'=>'work_type']) !!}
        </div>
    </div>
</div>
<button class="btn btn-info float-right" onclick="showData()">Search</button>
<button class="btn btn-default float-right mr-2" onclick="clearData()">Clear</button>
