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
                                <a href="{{ route('interview.create') }}"><button class="btn btn-info float-right" type="button"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="interviewTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Hiring Manager</th>
                                    <th>Client</th>
                                    <th>Interview Date</th>
                                    <th>Interview Time</th>
                                    <th>Candidate Phone Number</th>
                                    <th>Candidate Email</th>
                                    <th>Time Zone</th>
                                    <th>Status</th>
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
        var table = $('#interviewTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('interview.index') }}",
            columns: [
                {data: 'DT_RowIndex', 'width': '6%', name: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'hiring_manager', name: 'hiring_manager'},
                {data: 'client', name: 'client'},
                {data: 'interview_date', name: 'interview_date'},
                {data: 'interview_time', name: 'interview_time'},
                {data: 'candidate_phone_number', name: 'candidate_phone_number'},
                {data: 'candidate_email', name: 'candidate_email'},
                {data: 'time_zone', name: 'time_zone'},
                {data: 'status', name: 'status'},
                {data: 'action', name: 'action', orderable: false, searchable: false},
            ]
        });

        $('#interviewTable tbody').on('change', '.interviewStatus', function (event) {
            event.preventDefault();
            var interviewId = $(this).attr("data-id");
            var status = $(this).val();
            swal({
                title: "Are you sure?",
                text: "You want to update the status for this Interview?",
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
                        url: "{{url('admin/interview/changeInterviewStatus')}}/"+interviewId,
                        type: "POST",
                        data: {'status':status, _token: '{{csrf_token()}}' },
                        success: function(data){
                            if(data == 1){
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