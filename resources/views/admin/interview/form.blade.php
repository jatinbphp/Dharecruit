{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('hiring_manager') ? ' has-error' : '' }}">
            <label class="control-label" for="hiring_manager">Enter Hiring Manager Name :</label>
            {!! Form::text('hiring_manager', null, ['class' => 'form-control', 'placeholder' => 'Enter Hiring Manager Name', 'id' => 'hiring_manager']) !!}
            @if ($errors->has('hiring_manager'))
                <span class="text-danger">
                    <strong>{{ $errors->first('hiring_manager') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('client') ? ' has-error' : '' }}">
            <label class="control-label" for="client">Enter Client Name :</label>
            {!! Form::text('client', null, ['class' => 'form-control', 'placeholder' => 'Enter Client Name', 'id' => 'client', 'readonly' => true]) !!}
            @if ($errors->has('client'))
                <span class="text-danger">
                    <strong>{{ $errors->first('client') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('interview_date') ? ' has-error' : '' }}">
            <label class="control-label" for="interview_date">Select Interview Date :<span class="text-red">*</span></label>
            {!! Form::date('interview_date', null, ['class' => 'form-control', 'placeholder' => 'Select Interview Date ', 'id' => 'interview_date']) !!}
            @if ($errors->has('interview_date'))
                <span class="text-danger">
                    <strong>{{ $errors->first('interview_date') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('interview_time') ? ' has-error' : '' }}">
            <label class="control-label" for="interview_time">Select Interview Time :<span class="text-red">*</span></label>
            {!! Form::time('interview_time', null, ['class' => 'form-control', 'placeholder' => 'Select Interview Time ', 'id' => 'interview_time']) !!}
            @if ($errors->has('interview_time'))
                <span class="text-danger">
                    <strong>{{ $errors->first('interview_time') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('candidate_phone_number') ? ' has-error' : '' }}">
            <label class="control-label" for="candidate_phone_number">Enter Candidate Phone :<span class="text-red">*</span></label>
            {!! Form::text('candidate_phone_number', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Phone ', 'id' => 'candidate_phone_number', 'readonly' => true]) !!}
            @if ($errors->has('candidate_phone_number'))
                <span class="text-danger">
                    <strong>{{ $errors->first('candidate_phone_number') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('candidate_email') ? ' has-error' : '' }}">
            <label class="control-label" for="candidate_email">Enter Candidate Email :<span class="text-red">*</span></label>
            {!! Form::text('candidate_email', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Email ', 'id' => 'candidate_email', 'readonly' => true]) !!}
            @if ($errors->has('candidate_email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('candidate_email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('time_zone') ? ' has-error' : '' }}">
            <label class="control-label" for="time_zone">Enter Time Zone :<span class="text-red">*</span></label>
            {!! Form::text('time_zone', null, ['class' => 'form-control', 'placeholder' => 'Enter Time Zone ', 'id' => 'time_zone']) !!}
            @if ($errors->has('time_zone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('time_zone') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="control-label" for="status">Status :<span class="text-red">*</span></label>
            {!! Form::select('status', $interviewStatus, null, ['class' => 'form-control select2','id'=>'status']) !!}
            @if ($errors->has('status'))
                <span class="text-danger">
                    <strong>{{ $errors->first('status') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
