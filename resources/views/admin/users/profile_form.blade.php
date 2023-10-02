<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    <label class="control-label" for="name">Name <span class="text-red">*</span></label>
    <div class="col-sm-8">
        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Name']) !!}
        @if ($errors->has('name'))
            <span class="help-block">
                <strong>{{ $errors->first('name')}}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    <label class="control-label" for="email">Email <span class="text-red">*</span></label>
    <div class="col-sm-8">
        {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'email']) !!}
        @if ($errors->has('email'))
            <span class="help-block">
                <strong>{{ $errors->first('email') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    <label class="control-label" for="inputPassword3">Password</label>
    <div class="col-sm-8">
        <input type="password" placeholder="Password" id="password" name="password" class="form-control" >
        @if ($errors->has('password'))
            <span class="help-block">
                <strong>{{ $errors->first('password') }}</strong>
            </span>
        @endif
    </div>
</div>

<div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
    <label class="control-label" for="inputPassword3">Confirm Password</label>
    <div class="col-sm-8">
        <input type="password" placeholder="Confirm password" id="password-confirm" name="password_confirmation" class="form-control" >
        @if ($errors->has('password_confirmation'))
            <span class="help-block">
             <strong>{{ $errors->first('password_confirmation') }}</strong>
            </span>
        @endif
    </div>
</div>

<script src="{{ URL::asset('assets/plugins/jQuery/jquery.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $("#profileForm").validate({
            ignore: ":hidden:not(.faqDescription),.note-editable.panel-body",
            rules: {
                name: "required",
                email: "required",
            },
            messages: {
                name: "The name field is required.",
                email: "The email field is required.",
            },
        });
        $('.error').css("font-weight", "bold")
    });
</script>
