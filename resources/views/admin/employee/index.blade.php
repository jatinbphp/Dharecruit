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
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('employee.create') }}"><button class="btn btn-info float-right" type="button" ><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                            </div> --}}
                        </div>
                        <div class="card-body table-responsive">
                            <table id="employeeTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Employer Name</th>
                                        <th>Employee Name</th>
                                        <th>Employee Email</th>
                                        <th>Employee Phone Number</th>
                                        <th style="width: 12.5%;">Status</th>
                                        <th style="width: 12.5%;">Action</th>
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
        var table = $('#employeeTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            ajax: "{{ route('employee.index') }}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'employee_name', name: 'employee_name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'status', "width": "12.5%", name: 'status'},
                {data: 'action', "width": "12.5%", name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#employeeTable tbody').on('click', '.deletePvCompany', function (event) {
            event.preventDefault();
            var roleId = $(this).attr("data-id");
            swal({
                    title: "Are you sure?",
                    text: "You want to delete this pv company?",
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
                        url: "{{url('admin/employee')}}/"+roleId,
                        type: "DELETE",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            table.row('.selected').remove().draw(false);
                            swal("Deleted", "Your data successfully deleted!", "success");
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });

        $('#employeeTable tbody').on('click', '.assign', function (event) {
            event.preventDefault();
            var employee_id = $(this).attr('uid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': employee_id},
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+employee_id).show();
                    $('#assign_add_'+employee_id).hide();
                    table.draw(false);
                }
            });
        });

        $('#employeeTable tbody').on('click', '.unassign', function (event) {
            event.preventDefault();
            var employee_id = $(this).attr('ruid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': employee_id,'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+employee_id).hide();
                    $('#assign_add_'+employee_id).show();
                    table.draw(false);
                }
            });
        });
    });
  </script>
@endsection
