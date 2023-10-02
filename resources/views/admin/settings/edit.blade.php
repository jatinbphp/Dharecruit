@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{url('/')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$menu}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @include ('admin.error')
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">Add {{$menu}}</h3>
                        </div>
                        {!! Form::model($settings,['url' => route('settings.update',['setting'=>1]),'method'=>'patch','id' => 'settingsForm','class' => 'form-horizontal','files'=>true]) !!}
                        <div class="card-body">
                           @include ('admin.settings.form')
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('dashboard') }}" ><button class="btn btn-default" type="button">Back</button></a>
                            <button class="btn btn-info float-right" type="submit">Update</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
<script type="text/javascript">
    $('.delete-settings-image').on('click', function (event) {
        event.preventDefault();
        var filename = $(this).data("name");
        var id = $(this).data("id");
        swal({
            title: "Are you sure?",
            text: "Do you want to delete this image",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#DD6B55',
            confirmButtonText: 'Yes, Delete',
            cancelButtonText: "No, cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                deleteSettingsImage(filename, id);
            } else {
                swal("Cancelled", "Your data safe!", "error");
            }
        });
    });

    function deleteSettingsImage(filename, id, className, fieldName){
        $.ajax({
            url: "{{url('admin/delete_settings_image')}}",
            type: "POST",
            data: {_token: '{{csrf_token()}}', id: id, image: filename},
            success: function(data){
                swal({
                    title: "Success",
                    text: "Image successfully deleted!",
                    type: "success",
                    confirmButtonText: "OK",
                    confirmButtonClass: "btn-success",
                    buttonsStyling: false
                },
                function(isConfirm) {
                    if (isConfirm) {
                        location.reload();
                    } else {
                        swal("Cancelled", "Your data safe!", "error");
                    }
                });
            }
        });
    }

</script>
@endsection
