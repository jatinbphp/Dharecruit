@if(Session::has('success'))
    <div class="alert alert-success">
        <button data-dismiss="alert" class="close">&times;</button>
        {{Session::get('success')}}
    </div>
@elseif(Session::has('danger'))
    <div class="alert alert-danger">
        <button data-dismiss="alert" class="close">&times;</button>
        {{Session::get('danger')}}
    </div>
@elseif(Session::has('warning'))
    <div class="alert alert-warning">
        <button data-dismiss="alert" class="close">&times;</button>
        {{Session::get('warning')}}
    </div>
@endif
