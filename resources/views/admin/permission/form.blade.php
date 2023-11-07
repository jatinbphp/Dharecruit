{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
            <label class="control-label" for="type">User Type :<span class="text-red">*</span></label>
            {!! Form::text('type', null, ['class' => 'form-control', 'placeholder' => 'Enter User Type', 'id' => 'user_type','readonly']) !!}
            @if ($errors->has('type'))
                <span class="text-danger">
                    <strong>{{ $errors->first('type') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="col-md-12">
        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="status">Access Modules :<span class="text-red">*</span></label>
            <div class="row">
                @foreach (\App\Models\Permission::$permission as $key => $value)
                    <div class="col-md-3">
                        <label>
                            {!! Form::checkbox('access_modules[]', $key, !empty($selectedModules)?$selectedModules:null, ['class' => 'flat-red']) !!} <span style="margin-right: 10px">{{ $value }}</span>
                        </label>
                    </div>
                @endforeach
                @if ($errors->has('status'))
                    <span class="text-danger" id="statusError">
                        <strong>{{ $errors->first('status') }}</strong>
                    </span>
                @endif
            </div>
        </div>
    </div>
</div>
