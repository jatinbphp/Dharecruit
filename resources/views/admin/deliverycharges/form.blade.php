{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
            <label class="control-label" for="city">City <span class="text-red">*</span></label>
            {!! Form::text('city', null, ['class' => 'form-control', 'placeholder' => 'Enter City', 'id' => 'city', 'rows' => 3]) !!}
            @if ($errors->has('city'))
                <span class="text-danger">
                    <strong>{{ $errors->first('city') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('charge') ? ' has-error' : '' }}">
            <label class="control-label" for="charge">Charge <span class="text-red">*</span></label>
            {!! Form::text('charge', null, ['class' => 'form-control', 'placeholder' => 'Enter Charge', 'id' => 'charge', 'rows' => 3]) !!}
            @if ($errors->has('charge'))
                <span class="text-danger">
                    <strong>{{ $errors->first('charge') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="status">Status <span class="text-red">*</span></label>
            <div class="col-md-12">
                @foreach (\App\Models\Category::$status as $key => $value)
                    @php $checked = !isset($users) && $key == 'active'?'checked':''; @endphp
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
