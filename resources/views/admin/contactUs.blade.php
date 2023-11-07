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
                        <div class="card-body table-responsive">
                            <table id="contactTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Subject</th>
                                        <th>Message</th>
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

    <div class="modal fade" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Name: <span id="msgName"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="msgBody">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('jquery')
    <script type="text/javascript">
        $(function () {
            var table = $('#contactTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('contactUs') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '10%' },
                    {data: 'name', "width": "20%", name: 'name'},
                    {data: 'email', "width": "40%",name: 'email'},
                    {data: 'subject', "width": "40%", name: 'subject'},
                    {data: 'message', "width": "10%", name: 'message', orderable: false, searchable: false},
                    {data: 'action', "width": "10%", name: 'action', orderable: false, searchable: false},
                ]
            });

            $('#contactTable tbody').on('click', '.deleteContact', function (event) {
                event.preventDefault();
                var cId = $(this).attr("data-id");
                swal({
                    title: "Are you sure?",
                    text: "To delete this contact",
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
                            url: "{{url('admin/contactUs')}}/"+cId,
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

            $('#contactTable tbody').on('click', '.showMsg', function (event) {
                event.preventDefault();
                var cId = $(this).attr("data-id");
                $.ajax({
                    url: "{{route('contactUsMsg')}}",
                    type: "get",
                    data: {id:cId,_token: '{{csrf_token()}}' },
                    success: function(data){
                        if(data != ""){
                            $('#msgName').html(data.name);
                            $('#msgBody').html(data.msg);
                            $('#messageModal').modal('show');
                        }
                    }
                });
            });
        });
    </script>
@endsection
