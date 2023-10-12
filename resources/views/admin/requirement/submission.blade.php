@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Submissions</h1>
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
                            <table id="submissionTable" class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>Recruiters</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Location</th>
                                    <th>Employer Detail</th>
                                    <th>Resume</th>
                                    <th style="width: 15%;">Status</th>
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
            var table = $('#submissionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('requirement.show',['requirement'=>$requirement->id]) }}",
                columns: [
                    {data: 'user_id', "width": "15%", name: 'user_id'},
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'location', name: 'location'},
                    {data: 'employer_detail', name: 'employer_detail'},
                    {data: 'documents', "width": "15%", name: 'documents'},
                    {data: 'status', "width": "15%", name: 'status'},
                ]
            });

            $('#submissionTable tbody').on('change', '.submissionStatus', function (event) {
                event.preventDefault();
                var submissionId = $(this).attr("data-id");
                var status = $(this).val();
                swal({
                        title: "Are you sure?",
                        text: "You want to update the status for this submission?",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#DD6B55',
                        confirmButtonText: 'Yes, Update',
                        cancelButtonText: "No, cancel",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            $.ajax({
                                url: "{{url('admin/requirement/changeStatus')}}/"+submissionId,
                                type: "POST",
                                data: {'status':status, _token: '{{csrf_token()}}' },
                                success: function(data){
                                    console.log(data);
                                    if(data == 1){
                                        table.draw(false);
                                        swal("Success", "Status successfully updated!", "success");
                                    }else{
                                        swal("Error", "Something is wrong!", "error");
                                    }
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
