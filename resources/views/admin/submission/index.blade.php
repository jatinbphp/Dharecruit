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
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$menu}}</li>
                        </ol>
                    </div>
                </div>
            </div><!-- /.container-fluid -->
        </section>

        <!-- Main content -->
        <section class="content">
            @include ('admin.error')
            <div id="responce" class="alert alert-success" style="display: none">
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    {!! Form::open(['url' => route('requirement.index'), 'method' => 'get', 'id' => 'filterForm', 'class' => 'form-horizontal','files'=>true]) !!}
                                    @include('admin.filter')
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="requirementTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Job Title</th>
                                        <th>No # Position</th>
                                        <th>Experience</th>
                                        <th>Locations</th>
                                        <th>Work Type</th>
                                        <th>Duration</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('jquery')
<script type="text/javascript">
    $(function () {
        var table = $('#requirementTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('submission.index') }}",
            columns: [
                {data: 'job_title', name: 'job_title'},
                {data: 'no_of_position', name: 'no_of_position'},
                {data: 'experience', name: 'experience'},
                {data: 'location', name: 'location'},
                {data: 'work_type', name: 'work_type'},
                {data: 'duration', name: 'duration'},
                {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#requirementTable tbody').on('click', '.assignRequirement', function (event) {
            event.preventDefault();
            var requirementId = $(this).attr("data-id");
            var isAssign = $(this).attr("data-assign");
            swal({
                title: "Are you sure?",
                text: "You want to assign this requirement?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Assign',
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{url('admin/submission/assign/')}}/"+requirementId,
                        type: "POST",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            console.log(data);
                            swal("Success", "Requirement has been successfully assign!", "success");
                            table.draw(false);
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });
    });
  </script>
@endsection
