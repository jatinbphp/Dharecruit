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
            <div id="responce" class="alert alert-success" style="display: none;">
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('requirement.create') }}"><button class="btn btn-info float-right" type="button"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
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
                                        <th>Daily #</th>
                                        <th>Job Id</th>
                                        <th>Job Title</th>
                                        <th>BDM</th>
                                        <th>Duration</th>
                                        <th>Location</th>
                                        <th>Rate</th>
                                        <th>Onsite</th>
                                        <th>Category</th>
                                        <th>Timer</th>
                                        <th>Recruiter</th>
                                        <th>Status</th>
                                        <th>Color</th>
                                        <th>Candidate</th>
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
            responsive: true,
            ajax: "{{ $type == 1 ? route('requirement.index') : route('my_requirement') }}",
            columns: [
                {data: 'DT_RowIndex', 'width': '13%', name: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'job_id', 'width': '11%', name: 'job_id'},
                {data: 'job_title', 'width': '30%', name: 'job_title'},
                {data: 'user_id', 'width': '20%', name: 'user_id'},
                {data: 'duration', name: 'duration'},
                {data: 'location', name: 'location'},
                {data: 'vendor_rate', name: 'vendor_rate'},
                {data: 'work_type', name: 'work_type'},
                {data: 'category', name: 'category'},
                {data: 'created_at', name: 'created_at'},
                {data: 'recruiter', name: 'recruiter'},
                {data: 'status', name: 'status'},
                {data: 'color', name: 'color'},
                {data: 'candidate', name: 'candidate'},
                {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#requirementTable tbody').on('click', '.deleteRequirement', function (event) {
            event.preventDefault();
            var roleId = $(this).attr("data-id");
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this requirement?",
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
                    $.ajax({
                        url: "{{url('admin/requirement')}}/"+roleId,
                        type: "DELETE",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            console.log(data);
                            table.row('.selected').remove().draw(false);
                            swal("Deleted", "Your data successfully deleted!", "success");
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });

        $('#requirementTable tbody').on('click', '.assign', function (event) {
            event.preventDefault();
            var user_id = $(this).attr('uid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': user_id},
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+user_id).show();
                    $('#assign_add_'+user_id).hide();
                    table.draw(false);
                }
            });
        });

        $('#requirementTable tbody').on('click', '.unassign', function (event) {
            event.preventDefault();
            var user_id = $(this).attr('ruid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': user_id,'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+user_id).hide();
                    $('#assign_add_'+user_id).show();
                    table.draw(false);
                }
            });
        });
    });
  </script>
@endsection
