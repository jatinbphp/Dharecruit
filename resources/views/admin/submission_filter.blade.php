<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('candidateId') ? ' has-error' : '' }}">
            <label class="control-label" for="requirement">Candidate Id</label>
            {!! Form::text('candidateId', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Id', 'id' => 'candidateId']) !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('candidateEmail') ? ' has-error' : '' }}">
            <label class="control-label" for="requirement">Candidate Email</label>
            {!! Form::text('candidateEmail', null, ['class' => 'form-control', 'placeholder' => 'Enter Candidate Email', 'id' => 'candidateEmail']) !!}
        </div>
    </div>
</div>
<button class="btn btn-info float-right" onclick="showData()">Search</button>
<button class="btn btn-default float-right mr-2" onclick="clearData()">Clear</button>