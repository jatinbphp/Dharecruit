{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label class="control-label" for="name">Employer Name :<span class="text-red">*</span></label>
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter Employer Name', 'id' => 'name']) !!}
            @if ($errors->has('name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('employee_name') ? ' has-error' : '' }}">
            <label class="control-label" for="poc_name">Employee Name :<span class="text-red">*</span></label>
            {!! Form::text('employee_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Name', 'id' => 'employee_name']) !!}
            @if ($errors->has('employee_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('employee_name') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="control-label" for="email">Employee Email :<span class="text-red">*</span></label>
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Email', 'id' => 'email']) !!}
            @if ($errors->has('email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">Employee Phone Number :<span class="text-red">*</span></label>
            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Enter Employee Phone Number', 'id' => 'phone']) !!}
            @if ($errors->has('phone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('phone') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="status">Status :<span class="text-red">*</span></label>
            <div class="col-md-12">
                @foreach (\App\Models\PVCompany::$status as $key => $value)
                        <?php $checked = !isset($users) && $key == 'active'?'checked':'';?>
                    <label>
                        {!! Form::radio('status', $key, null, ['class' => 'flat-red',$checked]) !!} <span style="margin-right: 10px">{{ $value }}</span>
                    </label>
                @endforeach
                <br class="statusError">
                @if ($errors->has('status'))
                    <span class="text-danger" id="statusError">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
