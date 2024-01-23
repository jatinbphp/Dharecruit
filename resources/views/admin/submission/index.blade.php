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
                                    <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                </div>
                                <div class="col-md-12 border mt-3 pb-3 pt-3 pl-3 pb-3 pr-3" id="filterDiv">
                                    {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                    @include('admin.'.$filterFile)
                                    {!! Form::close() !!}
                                </div>
                            </div>
                            <div class="row mt-2">
                                <div class="col-md-3 mt-2">
                                    {!! Form::checkbox('', '', null, ['id' => 'showDate', 'class' => 'toggle-checkbox toggle-change', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                    <label class="form-check-label pl-2" for="showDate">Show Date</label>
                                </div>
                                @if(Auth::user()->role == 'recruiter')
                                    <div class="col-md-3 mt-2">
                                        {!! Form::checkbox('', '', null, ['id' => 'show_my_candidate', 'class' => 'toggle-checkbox toggle-change', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                        <label class="form-check-label pl-2" for=show_my_candidate">Show My Candidates Only</label>
                                    </div>
                                @endif
                                @if(in_array(Auth::user()->role, ['bdm', 'recruiter']))
                                    <div class="col-md-3 mt-2">
                                        {!! Form::checkbox('', '', null, ['id' => 'toggle_job_keyword', 'class' => 'toggle-checkbox toggle-change', 'checked' => false, 'data-toggle' => 'toggle', 'data-onstyle' => 'success', 'data-offstyle' => 'secondary', 'data-size' => 'small']) !!}
                                        <label class="form-check-label pl-2" for="toggle_job_keyword">Show Job Keyword</label>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="requirementTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Daily #</th>
                                        <th>J Id</th>
                                        <th>Job Title</th>
                                        <th>Location</th>
                                        <th>Onsite</th>
                                        <th>Duration</th>
                                        <th class="toggle-job-keyword-column">Job Keyword</th>
                                        <th>Category</th>
                                        <th>BDM</th>
                                        <th>Rate</th>
                                        <th>client</th>
                                        <!-- <th>Timer</th> -->
                                        <th>Recruiter</th>
                                        <th>Status</th>
                                        <!-- <th>Color</th> -->
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
    @if(Auth::user()->role == 'recruiter')
        @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>1,])
    @else
        @include('admin.requirement.candidateModal',['hide'=>0, 'isSubmission'=>0,])
    @endif
    @include('admin.viewSubmissionModel')
    @include('admin.updateSubmissionModel')
    @include('admin.submission.linking_empModel')
@endsection

@section('jquery')
<script type="text/javascript">
    $(document).ready(function () {
        datatables();
    });

    function showRequirementFilterData(){
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function clearRequirementData(){
        $('#filterForm')[0].reset();
        $('select').trigger('change');
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function datatables() {
        var table = $('#requirementTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            ajax: {
                url: "{{ $type == 1 ? route('submission.index') : route('my_submission') }}",
                data: function (d) {
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
                {data: 'DT_RowIndex', 'width': '4%', name: 'DT_RowIndex', orderable: false, searchable: false, render: function(data, type, full, meta){
                    var columnData = data;
                    var objectDate = new Date(full['created_at']);
                    let day = objectDate.getDate();
                    // Added 1 in month as in javascript month range is 0-11
                    let month = objectDate.getMonth() + 1;
                    let year = objectDate.getFullYear().toString().substr(-2);
                    columnData += '<p>'+ month +'/'+ day +'/'+ year +'</p>'
                    return columnData;
                }},
                {data: 'job_id', 'width': '8%', name: 'job_id'},
                {data: 'job_title', 'width': '15%', name: 'job_title'},
                {data: 'location', name: 'location'},
                {data: 'work_type', name: 'work_type'},
                {data: 'duration', name: 'duration'},
                {data: 'job_keyword', 'width': '15%', name: 'job_keyword'},
                {data: 'category_name', name: 'category.name'},
                {data: 'bdm_name', name: 'bdm.name'},
                {data: 'my_rate', name: 'my_rate'},
                {data: 'client_name', name: 'client_name', render: function(data, type, row) {
                        if (row.display_client == 1) {
                            return row.client_name;
                        }
                        return '';
                    }
                },
                // {data: 'created_at', 'width': '18%', name: 'created_at'},
                {data: 'recruiter', name: 'recruiter', orderable: false},
                {data: 'status', 'width': '10%', name: 'status'},
                // {data: 'color', name: 'color'},
                {data: 'candidate', name: 'candidate', orderable: false},
                {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
            ],
            order: [[1, 'desc']],
        });
        $('#requirementTable').on('draw.dt', function () {
            $('#requirementTable thead th.toggle-job-keyword-column').each(function() {
                var columnIndex = $(this).index();
                table.column(columnIndex).nodes().to$().addClass('toggle-job-keyword-column');
            });
            $('#toggle_job_keyword').trigger('change');
            $('.toggle-change').trigger('change');

        });
    }

    $(function () {
        $('#requirementTable tbody').on('click', '.assignRequirement', function (event) {
            event.preventDefault();
            var requirementId = $(this).attr("data-id");
            var isAssign = $(this).attr("data-assign");
            swal({
                title: "Are you sure?",
                text: "You want to assign this requirement?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#DD6B55',
                confirmButtonText: 'Yes, Assign',
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false,
            },
            function(isConfirm) {
                if (isConfirm) {
                    swal({
                        title: "Filling Confident",
                        text: "",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: '#b9b9b9',
                        confirmButtonText: 'Yes',
                        cancelButtonText: "Will Try",
                        closeOnConfirm: false,
                        closeOnCancel: false
                    },
                    function(isConfirm) {
                        var fillConfident = 1;
                        if (isConfirm) {
                            fillConfident = 2;
                        } else {
                            fillConfident = 1;
                        }
                        $.ajax({
                            url: "{{url('admin/submission/assign/')}}/"+requirementId,
                            type: "POST",
                            data: {'filling_confident': fillConfident,_token: '{{csrf_token()}}' },
                            success: function(data){
                                swal("Success", "Requirement has been successfully assign!", "success");
                                //table.draw(false);
                                $('#requirementTable').DataTable().ajax.reload();
                            }
                        });
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });
    });

    function addWaiting(id)
    {
        $.ajax({
            url: "{{url('admin/submission/waiting/')}}/"+id,
            type: "GET",
            data: {_token: '{{csrf_token()}}' },
            success: function(data){
                if(data.status == 1){
                    swal("Success", "Waiting Added successfully!", "success");
                } else {
                    swal("Error", "Something Went Wrong!", "error");
                }
                $('#requirementTable').DataTable().ajax.reload();
            }
        });
    }
  </script>
@endsection
