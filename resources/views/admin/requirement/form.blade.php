{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-4">
        <div class="form-group{{ $errors->has('job_title') ? ' has-error' : '' }}">
            <label class="control-label" for="job_title">Job Title :<span class="text-red">*</span></label>
            {!! Form::text('job_title', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Title', 'id' => 'job_title']) !!}
            @if ($errors->has('job_title'))
                <span class="text-danger">
                    <strong>{{ $errors->first('job_title') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('no_of_position') ? ' has-error' : '' }}">
            <label class="control-label" for="no_of_position">No # Position :<span class="text-red">*</span></label>
            {!! Form::text('no_of_position', null, ['class' => 'form-control', 'placeholder' => 'Enter No # Position', 'id' => 'no_of_position']) !!}
            @if ($errors->has('no_of_position'))
                <span class="text-danger">
                    <strong>{{ $errors->first('no_of_position') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('experience') ? ' has-error' : '' }}">
            <label class="control-label" for="experience">Experience :<span class="text-red">*</span></label>
            {!! Form::text('experience', null, ['class' => 'form-control', 'placeholder' => 'Enter Experience', 'id' => 'experience']) !!}
            @if ($errors->has('experience'))
                <span class="text-danger">
                    <strong>{{ $errors->first('experience') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-4">
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

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('work_type') ? ' has-error' : '' }}">
            <label class="control-label" for="work_type">Onsite/Hybrid/Remote :<span class="text-red"></span></label>
            {!! Form::text('work_type', null, ['class' => 'form-control', 'placeholder' => 'Onsite/Hybrid/Remote', 'id' => 'work_type']) !!}
            @if ($errors->has('work_type'))
                <span class="text-danger">
                    <strong>{{ $errors->first('work_type') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }}">
            <label class="control-label" for="duration">Duration :<span class="text-red"></span></label>
            {!! Form::text('duration', null, ['class' => 'form-control', 'placeholder' => 'Duration', 'id' => 'duration']) !!}
            @if ($errors->has('duration'))
                <span class="text-danger">
                    <strong>{{ $errors->first('duration') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('visa') ? ' has-error' : '' }}">
            <label class="control-label" for="visa">Visa :@if (empty($customers))<span class="text-red">*</span>@endif</label>
            {!! Form::text('visa', null, ['class' => 'form-control', 'placeholder' => 'Enter Visa', 'id' => 'visa']) !!}
            @if ($errors->has('visa'))
                <span class="text-danger">
                    <strong>{{ $errors->first('visa') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group{{ $errors->has('client') ? ' has-error' : '' }}">
            <label class="control-label" for="client">Client :<span class="text-red">*</span></label>
            {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter Client', 'id' => 'client']) !!}
            @if ($errors->has('client'))
                <span class="text-danger">
                    <strong>{{ $errors->first('client') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3">
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

    <div class="col-md-3">
        <div class="form-group{{ $errors->has('my_rate') ? ' has-error' : '' }}">
            <label class="control-label" for="my_rate">My Rate :<span class="text-red">*</span></label>
            {!! Form::text('my_rate', null, ['class' => 'form-control', 'placeholder' => 'Enter My Rate', 'id' => 'my_rate']) !!}
            @if ($errors->has('my_rate'))
                <span class="text-danger">
                    <strong>{{ $errors->first('my_rate') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('priority') ? ' has-error' : '' }}">
            <label class="control-label" for="priority">Priority <span class="text-red">*</span></label>
            {!! Form::select('priority', \App\Models\Requirement::$priority, null, ['class' => 'form-control select2','id'=>'priority']) !!}
            @if ($errors->has('priority'))
                <span class="text-danger">
                    <strong>{{ $errors->first('priority') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('term') ? ' has-error' : '' }}">
            <label class="control-label" for="term">Term <span class="text-red">*</span></label>
            {!! Form::select('term', \App\Models\Requirement::$term, null, ['class' => 'form-control select2','id'=>'term']) !!}
            @if ($errors->has('term'))
                <span class="text-danger">
                    <strong>{{ $errors->first('term') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-12" id="priorityReason" style="display: none;">
        <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
            <label class="control-label" for="reason">Reason :<span class="text-red"></span></label>
            {!! Form::text('reason', null, ['class' => 'form-control', 'placeholder' => 'Enter Reason', 'id' => 'reason']) !!}
            @if ($errors->has('reason'))
                <span class="text-danger">
                    <strong>{{ $errors->first('reason') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('category') ? ' has-error' : '' }}">
            <label class="control-label" for="category">Category <span class="text-red">*</span></label>
            {!! Form::select('category', $category, null, ['class' => 'form-control select2','id'=>'category']) !!}
            @if ($errors->has('category'))
                <span class="text-danger">
                    <strong>{{ $errors->first('category') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('moi') ? ' has-error' : '' }}">
            <label class="control-label" for="moi">MOI <span class="text-red">*</span></label>
            {!! Form::select('moi', $moi, null, ['class' => 'form-control select2','id'=>'moi']) !!}
            @if ($errors->has('moi'))
                <span class="text-danger">
                    <strong>{{ $errors->first('moi') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('job_keyword') ? ' has-error' : '' }}">
            <label class="control-label" for="job_keyword">Job Keyword :<span class="text-red">*</span></label>
            {!! Form::text('job_keyword', null, ['class' => 'form-control', 'placeholder' => 'Enter Job Keyword', 'id' => 'job_keyword']) !!}
            @if ($errors->has('job_keyword'))
                <span class="text-danger">
                    <strong>{{ $errors->first('job_keyword') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('notes') ? ' has-error' : '' }}">
            <label class="control-label" for="notes">Special Notes :<span class="text-red"></span></label>
            {!! Form::text('notes', null, ['class' => 'form-control', 'placeholder' => 'Enter Notes', 'id' => 'notes']) !!}
            @if ($errors->has('notes'))
                <span class="text-danger">
                    <strong>{{ $errors->first('notes') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
            <label class="control-label" for="notes">Job Description :<span class="text-red">*</span></label>
            {!! Form::textarea('description', null, ['class' => 'form-control', 'rows'=>4, 'placeholder' => 'Enter Job Description', 'id' => 'job_description']) !!}
            @if ($errors->has('description'))
                <span class="text-danger">
                    <strong>{{ $errors->first('description') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<hr>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('pv_company_name') ? ' has-error' : '' }}">
            <label class="control-label" for="pv_company_name">PV Company Name :<span class="text-red">*</span></label>
            {!! Form::select('pv_company_name', $pv_company, null, ['class' => 'form-control select2','id'=>'moi']) !!}
            @if ($errors->has('pv_company_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('pv_company_name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_name') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_name">POC Name :<span class="text-red">*</span></label>
            {!! Form::text('poc_name', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Name', 'id' => 'poc_name']) !!}
            @if ($errors->has('poc_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_name') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_email') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_email">POC Email :<span class="text-red">*</span></label>
            {!! Form::text('poc_email', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'poc_email']) !!}
            @if ($errors->has('poc_email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_phone_number') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_phone_number">POC Phone Number :<span class="text-red">*</span></label>
            {!! Form::text('poc_phone_number', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Phone Number', 'id' => 'poc_phone_number']) !!}
            @if ($errors->has('poc_phone_number'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_phone_number') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_location') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_location">POC Location :<span class="text-red"></span></label>
            {!! Form::text('poc_location', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Location', 'id' => 'poc_location']) !!}
            @if ($errors->has('poc_location'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_location') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_company_location') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_company_location">POC Company Location :<span class="text-red"></span></label>
            {!! Form::text('poc_company_location', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Company Location', 'id' => 'poc_company_location']) !!}
            @if ($errors->has('poc_company_location'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_company_location') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('client_name') ? ' has-error' : '' }}">
            <label class="control-label" for="client_name">Client Name :<span class="text-red">*</span></label>
            {!! Form::text('client_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Client Name', 'id' => 'client_name']) !!}
            @if ($errors->has('client_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('client_name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('display_client') ? ' has-error' : '' }}">
            <label class="control-label" for="display_client">Display Client :<span class="text-red"></span></label>
            <br>
            <div class="icheck-primary d-inline">
                <input type="checkbox" id="display_client">
                <label for="display_client"></label>
            </div>

            @if ($errors->has('display_client'))
                <span class="text-danger">
                    <strong>{{ $errors->first('display_client') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

@section('jquery')
    <script type="text/javascript">
        $('#priority').on('change', function(){
            if($(this).val() == 'high'){
                $('#priorityReason').show();
            }else{
                $('#priorityReason').hide();
            }
        });
    </script>
@endsection
