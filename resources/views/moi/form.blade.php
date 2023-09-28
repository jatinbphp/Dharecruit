<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="control-label" for="name">MOI Name <span class="text-red">*</span></label>
            {!! Form::text('name', !empty($mois) ? $mois->name : null, ['class' => 'form-control', 'placeholder' => 'Enter Moi Name', 'id' => 'name']) !!}
            @if ($errors->has('name'))
                <span class="text-danger">
                    <strong>{{ $errors->first('name') }}</strong>
                </span>
            @endif
        </div>
    </div>
</div>






