@extends('admin.layouts.app')
@section('content')
@php
    $userType = Auth::user()->role;
@endphp
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
                            <div class="col-md-10">
                                <div class="row">
                                    @foreach (\App\Models\Interview::$toggleOptions as $key => $value)
                                        @php
                                            if($userType == 'bdm'){
                                                if(in_array($key,\App\Models\Interview::$hideForBDA)){
                                                    continue;
                                                }
                                            } elseif($userType == 'recruiter'){
                                                if(in_array($key,\App\Models\Interview::$hideForReq)){
                                                    continue;
                                                }
                                            }
                                        @endphp
                                        <div class="col-md-3">
                                            <label>
                                                {!! Form::checkbox('', $key, null, ['id' => "$key", 'onChange' => 'toggleOptions("'.$key.'")']) !!} <span style="margin-right: 10px">{{ $value }}</span>
                                            </label>
                                        </div>
                                    @endforeach
                                    <div class="col-md-3">
                                        <label>
                                            {!! Form::checkbox('', 'show-time', null, ['id' => "showTime"]) !!} <span style="margin-right: 10px; color:#AC5BAD; font-weight:bold; ">Status Time</span>
                                        </label>
                                    </div>
                                    <div class="col-md-2">
                                        <label>
                                            {!! Form::checkbox('', 'show-feedback', null, ['id' => "showFeedback"]) !!} <span style="margin-right: 10px">Show FeedBack</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('interview.create') }}"><button class="btn btn-info float-right" type="button"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="interviewTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Id</th>
                                    <th>Job Id</th>
                                    <th>Candidate Name</th>
                                    <th>Candidate Phone</th>
                                    <th>Candidate Email</th>
                                    <th>Client Location</th>
                                    <th>Candidate Location</th>
                                    @if(in_array($userType,['admin','recruiter']))
                                        <th>BDM</th>
                                    @endif
                                    @if(in_array($userType,['admin','bdm']))
                                    <th>Recruiter</th>
                                    @endif
                                    <th>B Rate</th>
                                    <th>R Rate</th>
                                    <th>Employer</th>
                                    @if(in_array($userType,['admin','recruiter']))
                                        <th>EmpPOC</th>
                                    @endif
                                    @if(in_array($userType,['admin','bdm']))
                                        <th>POC</th>
                                        <th>PV</th>
                                    @endif
                                    <th>Hiring Manager</th>
                                    <th>Client</th>
                                    <th>Interview Time</th>
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
@if(Auth::user()->role == 'recruiter')
    @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>1,])
@else
    @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>0,])
@endif
@endsection

@section('jquery')
<script type="text/javascript">
    $(function () {
        var table = $('#interviewTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('interview.index') }}",
            columns: [
                {data: 'created_at', name: 'created_at'},
                {data: 'DT_RowIndex', 'width': '2%', name: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'job_id', name: 'job_id'},
                {data: 'candidate_name', name: 'candidate_name'},
                {data: 'candidate_phone_number', name: 'candidate_phone_number'},
                {data: 'candidate_email', name: 'candidate_email'},
                {data: 'client_location', name: 'client_location'},
                {data: 'candidate_location', name: 'candidate_location'},
                @if(in_array($userType,['admin','recruiter']))
                    {data: 'bdm', name: 'bdm'},    
                @endif
                @if(in_array($userType,['admin','bdm']))
                    {data: 'recruiter', name: 'recruiter'},
                @endif
                {data: 'br', name: 'br'},
                {data: 'rr', name: 'rr'},
                {data: 'employer_name', name: 'employer_name'},
                @if(in_array($userType,['admin','recruiter']))
                    {data: 'emp_poc', name: 'emp_poc'},
                @endif
                @if(in_array($userType,['admin','bdm']))
                    {data: 'poc_name', name: 'poc_name'},
                    {data: 'pv_name', name: 'pv_name'},
                @endif
                {data: 'hiring_manager', name: 'hiring_manager'},
                {data: 'client', name: 'client'},
                {data: 'interview_time', name: 'interview_time'},
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
                        success: function(responce){
                            if(responce.status == 1){
                                $('.statusUpdatedAt-'+responce.entity_type+'-'+responce.submission_id).html(responce.updated_date_html);
                                if($("#showTime").is(':checked')){
                                    $('.statusUpdatedAt-'+responce.entity_type+'-'+responce.submission_id).show();    
                                }
                                $('.candidate-'+interviewId).html(responce.updated_candidate_html);
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

        $('#showTime').click(function(){
            if($('#showTime').is(':checked')){
                $('.status-time').show();    
            } else {
                $('.status-time').hide();
            }
        });
    });
  </script>
@endsection