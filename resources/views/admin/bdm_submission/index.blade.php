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
                                        <div class="col-md-12">
                                            <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                        </div>
                                        <div class="col-md-12 border mt-3 pb-3 pt-3 pl-3 pb-3 pr-3" id="filterDiv">
                                            {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                            @include('admin.'.$filterFile)
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    <div class="row mt-2">
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
                                            <div class="col-md-3 mt-2">
                                                {!! Form::checkbox('', $key, null, ['id' => "$key", 'onChange' => 'toggleOptions("'.$key.'")', 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                                <label class="form-check-label pl-2" for="{{$key}}"> {{ $value }}</label>
                                            </div>
                                        @endforeach
                                        <div class="col-md-3 mt-2">
                                            {!! Form::checkbox('', 'show-time', null, ['id' => "showTime", 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                            <label class="form-check-label pl-2" for="showTime">Status Time</label>
                                        </div>
                                        <div class="col-md-3 mt-2">
                                            {!! Form::checkbox('', 'show-feedback', null, ['id' => "showFeedback", 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                            <label class="form-check-label pl-2" for="showFeedback">Show FeedBack</label>
                                        </div>
                                        @if(in_array(Auth::user()->role, ['admin', 'bdm']))
                                            <div class="col-md-3 mt-2">
                                                {!! Form::checkbox('', '', null, ['id' => 'toggle-poc', 'class' => 'toggle-checkbox', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                                <label class="form-check-label pl-2" for="showLink">Show POC</label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                        <div class="row">
                                <div class="col-4" id="pageLendthSection"></div>
                                <div class="col-5">
                                    <div class='float-right mt-3'>
                                        <label for="sort_data">Sort By Latest: </label>
                                        {!! Form::select('status', \App\Models\Submission::getSortOptions(), null, ['class' => 'form-control-sm select2','id'=>'sort_data', 'style'=>'width:200px']) !!}
                                    </div>
                                </div>
                                <div class="col-3" id="searchSection"></div>
                            </div>
                            <table id="mySubmissionTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th class='sub_id'>Sub Id</th>
                                        <th class='job_id'>Job Id</th>
                                        <th>Job Title</th>
                                        <th>Location</th>
                                        <th>Candidate Location</th>
                                        {{-- <th>Job Keyword</th>
                                        <th>Duration</th> --}}
                                        <th>Client</th>
                                        @if(in_array($userType,['admin','recruiter']))
                                            <th>BDM</th>
                                        @endif
                                        @if(in_array($userType,['admin','bdm']))
                                            <th class='toggle-column'>PV</th>
                                            <th class='toggle-column'>POC</th>
                                            <th>Recruiter</th>
                                        @endif
                                        <th>B Rate</th>
                                        <th>R Rate</th>
                                        <th>Candidate Name</th>
                                        <th>Emp Name</th>
                                        @if(in_array($userType,['admin','recruiter']))
                                            <th>EmpPOC</th>
                                        @endif
                                        <th class='bdm_status'>BDM Status</th>
                                        <th class='pv_status'>PV Status</th>
                                        <th class='client_status'>Client Status</th>
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
    var table;
    $(function () {
        if("{{session()->get('filter')}}"){
            $("#filter_status").select2("val", "{{session()->get('filter') }}");
        }
        dataTables();
        @if(Auth::user()->role == 'admin')
            $('#toggle-poc').bootstrapToggle('on');
        @endif
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

        $('#mySubmissionTable tbody').on('click', '.submissionPvStatus', function (event) {
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
       // $("#mySubmissionTable").dataTable().fnDestroy();
        // var selectedFilter = $("#filter_status").val();

        table = $('#mySubmissionTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            order: [[ 1, 'desc' ],],
            drawCallback: function(settings) {

                var headerClassName = $('#sort_data').val();
                if(headerClassName){
                    var columnIndex = $('.' + headerClassName).index();
                    $('#mySubmissionTable tbody td:nth-child(' + (columnIndex + 1) + ')').addClass('color-group');
                }
            },
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
                @if(in_array($userType,['admin','recruiter']))
                    {data: 'emp_poc',  name: 'emp_poc'},
                @endif
                {data: 'bdm_status', "width": "9%", name: 'bdm_status', searchable: false},
                {data: 'pv_status', "width": "10%", name: 'pv_status', searchable: false},
                {data: 'client_status', "width": "10%", name: 'client_status', searchable: false},
                // {data: 'action', "width": "9%", name: 'action', orderable: false, searchable: false},
            ],
            initComplete: function(settings, json) {
                $('#mySubmissionTable thead th.toggle-column').each(function() {
                    var columnIndex = $(this).index();
                    table.column(columnIndex).nodes().to$().addClass('toggle-column');
                });
                $('#toggle-poc').trigger('change');
                $("#mySubmissionTable_length").detach().appendTo("#pageLendthSection");
                $("#mySubmissionTable_filter").addClass('float-right').detach().appendTo("#searchSection");
                $('select[name="mySubmissionTable_length"]').css({
                    'width': 'auto',
                });
                $('#mySubmissionTable_length').css({
                    'display': 'flex',
                }).addClass('mt-4');
            },
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

    $('#sort_data').on('change', function(){
        var headerClassName = $('#sort_data').val();
        var columnIndex = $('.' + headerClassName).index();
        table.order([columnIndex, 'desc']).draw();
    });

    @if(isset($pvCompanyName) && $pvCompanyName)
       var availablePvCompanyName = <?php echo json_encode($pvCompanyName);?>;
        $(document).on('focusout keydown', '#pv_company', function (index, value) {
            $("#pv_company").autocomplete({
                source: availablePvCompanyName,
                minLength: 4
            });
        });
    @endif
  </script>
@endsection
