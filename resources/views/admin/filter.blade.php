<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('date') ? ' has-error' : '' }}">
            <label class="control-label" for="date">From & To Date</label>
            <div class="input-group">
                <div class="input-group-prepend">
                                                            <span class="input-group-text">
                                                                <i class="far fa-calendar-alt"></i>
                                                            </span>
                </div>
                {!! Form::text('date', null, ['class' => 'form-control float-right', 'placeholder' => 'Select From & To Date', 'id' => 'reservation']) !!}
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
            {!! Form::text('bdm', null, ['class' => 'form-control', 'placeholder' => 'Enter BDM', 'id' => 'bdm']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('recruiter') ? ' has-error' : '' }}">
            <label class="control-label" for="recruiter">Recruiter Search</label>
            {!! Form::text('recruiter', null, ['class' => 'form-control', 'placeholder' => 'Enter Recruiter', 'id' => 'recruiter']) !!}
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('poc_email') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_email">POC Email</label>
            {!! Form::text('poc_email', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'poc_email']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('pv_company') ? ' has-error' : '' }}">
            <label class="control-label" for="pv_company">PV Company</label>
            {!! Form::text('pv_company', null, ['class' => 'form-control', 'placeholder' => 'Enter PV Company', 'id' => 'pv_company']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('moi') ? ' has-error' : '' }}">
            <label class="control-label" for="moi">MOI</label>
            {!! Form::text('moi', null, ['class' => 'form-control', 'placeholder' => 'Enter MOI', 'id' => 'moi']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('remote') ? ' has-error' : '' }}">
            <label class="control-label" for="remote">Remote</label>
            {!! Form::text('remote', null, ['class' => 'form-control', 'placeholder' => 'Enter Remote', 'id' => 'remote']) !!}
        </div>
    </div>
</div>
<button class="btn btn-info float-right">Search</button>
