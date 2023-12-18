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
                                <div class="col-md-2">
                                    <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" value="" id="showDate">
                                        <label class="form-check-label" for="showDate">Show Date</label>
                                    </div>
                                </div>
                                @if(Auth::user()->role == 'recruiter')
                                    <div class="col-md-2">
                                        <div class="form-check mt-2">
                                            <input class="form-check-input" type="checkbox" value="" id="show_my_candidate">
                                            <label class="form-check-label" for="show_my_candidate">Show My Candidates Only</label>
                                        </div>
                                    </div>
                                @endif
                                <div class="col-md-12 border mt-3 pb-3 pt-3 pl-3 pb-3 pr-3" id="filterDiv">
                                    {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                    @include('admin.'.$filterFile)
                                    {!! Form::close() !!}
                                </div>
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
                                        <th>Job Keyword</th>
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
                {data: 'job_title', 'width': '30%', name: 'job_title'},
                {data: 'location', name: 'location'},
                {data: 'work_type', name: 'work_type'},
                {data: 'duration', name: 'duration'},
                {data: 'job_keyword', 'width': '15%', name: 'job_keyword'},
                {data: 'category', name: 'category'},
                {data: 'user_id', 'width': '6%', name: 'user_id'},
                {data: 'my_rate', name: 'my_rate'},
                {data: 'client', name: 'client'},
                // {data: 'created_at', 'width': '18%', name: 'created_at'},
                {data: 'recruiter', name: 'recruiter'},
                {data: 'status', 'width': '10%', name: 'status'},
                // {data: 'color', name: 'color'},
                {data: 'candidate', name: 'candidate'},
                {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
            ]
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
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{url('admin/submission/assign/')}}/"+requirementId,
                        type: "POST",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            swal("Success", "Requirement has been successfully assign!", "success");
                            //table.draw(false);
                            $('#requirementTable').DataTable().ajax.reload();
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
