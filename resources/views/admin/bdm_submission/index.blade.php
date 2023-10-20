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
            <div class="col-md-3">
                <div class="form-group">
                    <label class="control-label" for="filter_status">Filter</label>
                    {!! Form::select('null', $filterOptions, 'null', ['class' => 'form-control select2','id'=>'filter_status']) !!}
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-body table-responsive">
                            <table id="mySubmissionTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Job Id</th>
                                        <th>Job Title</th>
                                        <th>Location</th>
                                        <th>Job Keyword</th>
                                        <th>Duration</th>
                                        <th>client</th>
                                        <th>Recruiter</th>
                                        <th>Candidate Name</th>
                                        <th>Employer Name</th>
                                        <th>Action</th>
                                        <th>Status</th>
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
        <div class="modal fade bd-example-modal-lg" id="pvreasonModal" tabindex="-1" role="dialog" aria-labelledby="pvreasonModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="pvreasonModalLabel">Reason For Rejection</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    {!! Form::open(['url' => route('pv_reject_reason_update.update'), 'id' => 'candidateForm', 'class' => 'form-horizontal','files'=>true]) !!}
                    {!! Form::hidden('submissionId', null, ['class' => 'form-control', 'id' => 'submissionId']) !!}
                    {!! Form::hidden('pv_status', null, ['class' => 'form-control', 'id' => 'pv_status']) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12 rejection">
                                        <div class="form-group">
                                            <label class="control-label" for="reason">Pv Rejection Reason :</label>
                                            {!! Form::textarea('pv_reason', null, ['class' => 'form-control', 'rows'=>4, 'placeholder' => 'Pv Rejection Reason', 'id' => 'pv_reason']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" id="status_submit" class="btn btn-primary">Submit</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>0,])
@endsection

@section('jquery')
<script type="text/javascript">
    $(function () {
        dataTable();
        $('#mySubmissionTable tbody').on('change', '.submissionStatus', function (event) {
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
                            if(data == 1){
                                swal("Success", "Status successfully updated!", "success");
                            }else{
                                swal("Error", "Something is wrong!", "error");
                            }
                            dataTable();
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });

        $('#mySubmissionTable tbody').on('change', '.submissionPvStatus', function (event) {
            event.preventDefault();
            var submissionId = $(this).attr("data-id");
            console.log(submissionId);
            var status = $(this).val();
            console.log(status);
            swal({
                title: "Are you sure?",
                text: "You want to update the pv status for this submission?",
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
                    if(status == 'rejected_by_pv'){
                        console.log('called for action');
                        $("#submissionId").val(submissionId);
                        $("#pv_status").val(status);
                        //swal.close();
                        // $('#submissionId').val(submissionId);
                        // $("#candidateStatus").select2("val", 'rejected');
                        $('#pvreasonModal').modal('show');
                    }
                    $.ajax({
                        url: "{{url('admin/bdm_submission/changePvStatus')}}/"+submissionId,
                        type: "POST",
                        data: {'pv_status':status, _token: '{{csrf_token()}}' },
                        success: function(data){
                            if(data == 1){
                                swal("Success", "PV Status successfully updated!", "success");
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
    function dataTable(){
        $("#mySubmissionTable").dataTable().fnDestroy();
        var selectedFilter = $("#filter_status").val();
        
        var table = $('#mySubmissionTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ route('bdm_submission.index') }}",
                data: function (d) {
                    d.filter_status = selectedFilter;
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                {data: 'job_id', name: 'job_id'},
                {data: 'job_title', 'width': '20%', name: 'job_title'},
                {data: 'location', name: 'location'},
                {data: 'job_keyword', 'width': '18%',  name: 'job_keyword'},
                {data: 'duration',  name: 'duration'},
                {data: 'client_name',  name: 'client_name'},
                {data: 'recruter_name',  name: 'recruter_name'},
                {data: 'candidate_name',  name: 'candidate_name'},
                {data: 'employer_name',  name: 'employer_name'},
                {data: 'action', "width": "18%", name: 'action', orderable: false, searchable: false},
                {data: 'status', "width": "18%", name: 'status', orderable: false, searchable: false},
            ]
        });
    }

    $('#filter_status').on('change', function(){
        dataTable();
    });
  </script>
@endsection
