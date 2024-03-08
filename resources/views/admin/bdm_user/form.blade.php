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
            <label class="control-label" for="first_name">Email :<span class="text-red">*</span></label>
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter Email', 'id' => 'email']) !!}
            @if ($errors->has('email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3">
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

    <div class="col-md-3">
        <div class="form-group{{ $errors->has('indian_phone') ? ' has-error' : '' }}">
            <label class="control-label" for="indian_phone">Indian Phone :<span class="text-red"></span></label>
            {!! Form::text('indian_phone', null, ['class' => 'form-control', 'placeholder' => 'Enter Indian Phone', 'id' => 'indian_phone']) !!}
            @if ($errors->has('indian_phone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('indian_phone') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="status">Status :<span class="text-red">*</span></label>
            <div class="col-md-12">
                @foreach (\App\Models\Admin::$status as $key => $value)
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
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('is_allow_transfer_key') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="is_allow_transfer_key">Is Allow Transfer Key :</label>
            <div class="col-md-12">
                <label>
                    {!! Form::radio('is_allow_transfer_key', 1, null, ['class' => 'flat-red']) !!} <span style="margin-right: 10px">Yes</span>
                </label>
                <label>
                    {!! Form::radio('is_allow_transfer_key', 0, null, ['class' => 'flat-red']) !!} <span style="margin-right: 10px">No</span>
                </label>
                @if ($errors->has('is_allow_transfer_key'))
                    <span class="text-danger">
                        <strong>{{ $errors->first('is_allow_transfer_key') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label" for="password">Password :@if (empty($customers))<span class="text-red">*</span>@endif</label>
            {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Enter Password', 'id' => 'password']) !!}
            @if ($errors->has('password'))
                <span class="text-danger">
                    <strong>{{ $errors->first('password') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
            <label class="control-label" for="password">Confirm Password :@if (empty($customers))<span class="text-red">*</span>@endif</label>
            {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirm password', 'id' => 'password-confirm']) !!}
            @if ($errors->has('password_confirmation'))
                <span class="text-danger">
                    <strong>{{ $errors->first('password_confirmation') }}</strong>
                </span>
            @endif
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('color') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="color">Color :</label>
            <div class="input-group my-colorpicker2 colorpicker-element" data-colorpicker-id="color">
                {!! Form::text('color', null, ['class' => 'form-control', 'placeholder' => 'Enter Color', 'id' => 'color']) !!}
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-square" style="color: @if(isset($bdm_user['color'])) {{$bdm_user['color']}} @endif "></i></span>
                </div>
            </div>
            @if ($errors->has('color'))
                <span class="text-danger">
                    <strong>{{ $errors->first('color') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>
