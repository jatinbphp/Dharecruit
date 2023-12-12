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
            <div class="col-md-3">
                <!-- <div class="form-group">
                    <label class="control-label" for="filter_status">Filter</label>
                    {!! Form::select('filter[]', $filterOptions, 'null', ['multiple' => true,'class' => 'form-control select2','id'=>'filter_status','data-placeholder'=>'Please Select Filetr']) !!}
                </div> -->
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="row">
                                        <div class="col-md-2">
                                            <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                        </div>
                                        @foreach (\App\Models\Submission::$toggleOptions as $key => $value)
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
                                            <div class="col-md-2">
                                                <label>
                                                    {!! Form::checkbox('', $key, null, ['id' => "$key", 'onChange' => 'toggleOptions("'.$key.'")']) !!} <span style="margin-right: 10px">{{ $value }}</span>
                                                </label>
                                            </div>
                                        @endforeach
                                        <div class="col-md-2">
                                            <label>
                                                {!! Form::checkbox('', 'show-time', null, ['id' => "showTime"]) !!} <span style="margin-right: 10px; color:#AC5BAD; font-weight:bold; ">Status Time</span>
                                            </label>
                                        </div>
                                        <div class="col-md-2">
                                            <label>
                                                {!! Form::checkbox('', 'show-feedback', null, ['id' => "showFeedback"]) !!} <span style="margin-right: 10px">Show FeedBack</span>
                                            </label>
                                        </div>
                                        <div class="col-md-12 border mt-3 pb-3" id="filterDiv">
                                            {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                            @include('admin.'.$filterFile)
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="mySubmissionTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Sub Id</th>
                                        <th>Job Id</th>
                                        <th>Job Title</th>
                                        <th>Location</th>
                                        <th>Candidate Location</th>
                                        {{-- <th>Job Keyword</th>
                                        <th>Duration</th> --}}
                                        <th>Client</th>
                                        @if(in_array($userType,['admin','recruiter']))
                                            <th>EmpPOC</th>
                                            <th>BDM</th>
                                        @endif
                                        @if(in_array($userType,['admin','bdm']))
                                            <th>PV</th>
                                            <th>POC</th>
                                            <th>Recruiter</th>
                                        @endif
                                        <th>B Rate</th>
                                        <th>R Rate</th>
                                        <th>Candidate Name</th>
                                        <th>Emp Name</th>
                                        <th>BDM Status</th>
                                        <th>PV Status</th>
                                        <th>Client Status</th>
                                        {{-- <th>Action</th> --}}
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
                    {!! Form::hidden('filter', null, ['class' => 'form-control', 'id' => 'filter']) !!}
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
    @if(Auth::user()->role == 'recruiter')
        @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>1,])
    @else
        @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>0,])
    @endif
    @include('admin.updateSubmissionModel')
    @include('admin.submission.linking_empModel')
@endsection

@section('jquery')
<script type="text/javascript">
    $(function () {
        if("{{session()->get('filter')}}"){
            $("#filter_status").select2("val", "{{session()->get('filter') }}");
        }
        dataTables();
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
                            // dataTable();
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
            var status = $(this).val();

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
                        swal.close();
                        $("#submissionId").val(submissionId);
                        $("#pv_status").val(status);
                        $("#filter").val($("#filter_status").val());
                        $('#pvreasonModal').modal('show');
                    }
                    $.ajax({
                        url: "{{url('admin/bdm_submission/changePvStatus')}}/"+submissionId,
                        type: "POST",
                        data: {'pv_status':status, _token: '{{csrf_token()}}' },
                        success: function(data){
                            if(data.status == 1){
                                $(".candidate-"+submissionId).removeAttr("style");
                                $(".candidate-"+submissionId).removeClass().addClass("candidate-"+submissionId);
                                $(".candidate-"+submissionId).addClass(data.class);
                                $(".candidate-"+submissionId).attr('style', 'border-bottom :'+data.css);
                                $('.statusUpdatedAt-'+data.entity_type+'-'+submissionId).html(data.updated_date_html);
                                if($("#showTime").is(':checked')){
                                    $('.statusUpdatedAt-'+data.entity_type+'-'+submissionId).show();    
                                }
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

    function showRequirementFilterData(){
        $("#mySubmissionTable").dataTable().fnDestroy();
        dataTables();
    }

    function clearRequirementData(){
        $('#filterForm')[0].reset();
        $('select').trigger('change');
        $("#mySubmissionTable").dataTable().fnDestroy();
        dataTables();
    }

    function dataTables(){
        $('#showTime').prop('checked', false);
        $("#mySubmissionTable").dataTable().fnDestroy();
        // var selectedFilter = $("#filter_status").val();
        
        var table = $('#mySubmissionTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            order: [[ 1, 'desc' ],],
            ajax: {
                url: "{{ route('bdm_submission.index') }}",
                data: function (d) {
                    // d.filter_status = selectedFilter;
                    // d._token = '{{ csrf_token() }}';
                    var formDataArray = $('#filterForm').find(':input:not(select[multiple])').serializeArray();

                    // Filter out non-multiple-select elements and create a single array
                    var multipleSelectValues = $('#filterForm select[multiple]').map(function () {
                        return { name: $(this).attr('name'), value: $(this).val() };
                    }).get();

                    formDataArray = formDataArray.concat(multipleSelectValues);

                    var formData = {};
                    $.each(formDataArray, function(i, field){
                        formData[field.name] = field.value;
                    });
                    d = $.extend(d, formData);
                    return d;
                }
            },
            columns: [
                {data: 'created_at', name: 'created_at'},
                {data: 'id', name: 'id'},
                {data: 'job_id', name: 'job_id'},
                {data: 'job_title', 'width': '10%', name: 'job_title'},
                {data: 'location', name: 'location'},
                {data: 'candidate_location', name: 'candidate_location'},
                // {data: 'job_keyword', 'width': '10%',  name: 'job_keyword'},
                // {data: 'duration',  name: 'duration'},
                {data: 'client_name',  name: 'client_name'},
                @if(in_array($userType,['admin','recruiter']))
                    {data: 'emp_poc',  name: 'emp_poc'},
                    {data: 'bdm',  name: 'bdm'},
                @endif
                @if(in_array($userType,['admin','bdm']))
                    {data: 'pv',  name: 'pv'},
                    {data: 'poc',  name: 'poc'},
                    {data: 'recruter_name',  name: 'recruter_name'},
                @endif
                {data: 'b_rate',  name: 'b_rate'},
                {data: 'r_rate',  name: 'r_rate'},
                {data: 'candidate_name',  name: 'candidate_name'},
                {data: 'employer_name',  name: 'employer_name'},
                {data: 'bdm_status', "width": "9%", name: 'bdm_status', searchable: false},
                {data: 'pv_status', "width": "10%", name: 'pv_status', searchable: false},
                {data: 'client_status', "width": "10%", name: 'client_status', searchable: false},
                // {data: 'action', "width": "9%", name: 'action', orderable: false, searchable: false},
            ]
        });
    }

    $('#filter_status').on('change', function(){
        dataTables();
    });

    $('#showTime').click(function(){
        if($('#showTime').is(':checked')){
            $('.status-time').show();    
        } else {
            $('.status-time').hide();
        }
    });

    function showStatusOptions(id) {
        $('.show-pv-status-'+id).hide();
        $('.pv-status-'+id).show();
    }
  </script>
@endsection
