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

@if(getLoggedInUserRole() == 'bdm' && isset($user) && $user->is_allow_transfer_key == 1)
    <div class="form-group{{ $errors->has('transfer_key') ? ' has-error' : '' }}">
    <label class="control-label" for="transferKey">Generate Kay</label>
    <div class="col-sm-8">
        <div class="input-group mb-3">
            {!! Form::text('transfer_key', null, ['class' => 'form-control','id' => 'transferKey', 'readonly' => true]) !!}
            <div class="input-group-append">
                <button type="button" class="btn btn-sm btn-default" title="Generate Key" onclick="generateKey()"><i class="fa fa-redo" aria-hidden="true"></i></button>
                <button type="button" class="btn btn-sm btn-default" data-toggle="tooltip" data-placement="top" title="Copy to Clipboard" onclick="copyKey()" id="copyTooltip"><i class="fa fa-copy" aria-hidden="true"></i></button>
            </div>
        </div>
        @if ($errors->has('transfer_key'))
            <span class="help-block">
             <strong>{{ $errors->first('transfer_key') }}</strong>
            </span>
        @endif
    </div>
</div>
@endif

@section('jquery')
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

    function generateKey() {
        $.ajax({
            url: "{{route('profile_update.getTransferKey')}}",
            type: "get",
            success: function(data){
                if(data){
                    $("#transferKey").val(data);
                }
            }
        });
    }

    function copyKey() {
        $("#transferKey").select();
        document.execCommand('copy');
        $("#copyMessage").fadeIn(300).delay(1500).fadeOut(300);
    }
</script>
@endsection
