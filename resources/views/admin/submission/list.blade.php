@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$sub_menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Home</a></li>
                            <li class="breadcrumb-item active">{{$sub_menu}}</li>
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
                                <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                    <a href="{{ route('submission.newAdd',['id'=>$requirement['id']]) }}"><button class="btn btn-info float-right" type="button" ><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                                <div class="col-md-12 mt-3 pb-3" id="filterDiv">
                                    {!! Form::open(['id' => 'filterSubmissionForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                        @include('admin.submission_filter')
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="submissionTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Location</th>
                                        <th>Employer Detail</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                            <a href="{{route('submission.index')}}" class="btn btn-default">Back</a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('admin.requirement.candidateModal',['hide'=>0,'isSubmission'=>1])
@endsection

@section('jquery')
    <script type="text/javascript">
        $(document).ready(function () {
            datatables();
        });

        function showData(){
            $("#submissionTable").dataTable().fnDestroy();
            datatables();
        }

        function clearData(){
            $('#filterSubmissionForm')[0].reset();
            $("#submissionTable").dataTable().fnDestroy();
            datatables();
        }

        function datatables(){
            var table = $('#submissionTable').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('submission.show',['submission'=>$requirement['id']]) }}",
                    data: function (d) {
                        d.candidateId = $('#candidateId').val();
                        d.candidateEmail = $('#candidateEmail').val();
                        d._token = '{{ csrf_token() }}';
                    },
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'phone', name: 'phone'},
                    {data: 'location', name: 'location'},
                    {data: 'employer_detail', name: 'employer_detail'},
                    {data: 'status', name: 'status'},
                    {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
                ]
            });
        }

        $('#submissionTable tbody').on('click', '.view', function (event) {
            var cId = $(this).attr('data-cid');
            $.ajax({
                url: "{{route('get_candidate')}}",
                type: "post",
                data: {'cId': cId, 'isSubmission': '1' ,'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    if(data.status == 1){
                        var submission = data.submission;
                        if(submission.status != 'rejected'){
                            $('.rejected-status').hide();
                        } else {
                            $('.rejected-status').show();
                        }
                        $('#jobTitle').html(submission.requirement.job_title);
                        $('#submissionId').val(cId);
                        $("#reason").val(submission.reason);
                        $('#requirementData').html(data.requirementData);
                        $('#candidateData').html(data.candidateData);
                        $('#common-skill').html(submission.common_skills);
                        $('#skill-match').html(submission.skills_match);
                        $('#other-reason').html(submission.reason);
                        $('#status').html(submission.status[0].toUpperCase() + submission.status.slice(1))
                        if(submission.pv_status){
                            var pvStatus = submission.pv_status.replace(/_/g, ' ');
                            $('#pv_status_data').html(pvStatus[0].toUpperCase() + pvStatus.slice(1));
                        }
                        addSubmissionData(data);
                        $('#candidateModal').modal('show');
                    }else{
                        swal("Cancelled", "Something is wrong. Please try again!", "error");
                    }
                }
            });
        });
    </script>
@endsection
