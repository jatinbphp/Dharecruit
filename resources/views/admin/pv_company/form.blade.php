{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
            <label class="control-label" for="name">PV Company Name :<span class="text-red">*</span></label>
            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Enter PV Company Name', 'id' => 'name']) !!}
            @if ($errors->has('name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('name') }}</strong>
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

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
            <label class="control-label" for="email">POC Email :<span class="text-red">*</span></label>
            {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Email', 'id' => 'email', 'readonly' => isset($pv_company) ? true : false]) !!}
            @if ($errors->has('email'))
                <span class="text-danger">
                    <strong>{{ $errors->first('email') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('phone') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">POC Phone Number :<span class="text-red">*</span></label>
            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Phone Number', 'id' => 'phone']) !!}
            @if ($errors->has('phone'))
                <span class="text-danger">
                    <strong>{{ $errors->first('phone') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('poc_location') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">POC Location :<span class="text-red">*</span></label>
            {!! Form::text('poc_location', null, ['class' => 'form-control', 'placeholder' => 'Enter POC Location', 'id' => 'poc_location']) !!}
            @if ($errors->has('poc_location'))
                <span class="text-danger">
                    <strong>{{ $errors->first('poc_location') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('pv_company_location') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">PV Company Location :<span class="text-red">*</span></label>
            {!! Form::text('pv_company_location', null, ['class' => 'form-control', 'placeholder' => 'Enter PV Company Location', 'id' => 'pv_company_location']) !!}
            @if ($errors->has('pv_company_location'))
                <span class="text-danger">
                    <strong>{{ $errors->first('pv_company_location') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group{{ $errors->has('client_name') ? ' has-error' : '' }}">
            <label class="control-label" for="phone">Client Name :<span class="text-red">*</span></label>
            {!! Form::text('client_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Client Name', 'id' => 'client_name']) !!}
            @if ($errors->has('client_name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('client_name') }}</strong>
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
