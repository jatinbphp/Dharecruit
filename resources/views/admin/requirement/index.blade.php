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
            <div class="row">
                <div class="col-12">
                    <div class="card card-info card-outline">
                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-2">
                                    <button class="btn btn-info" type="button" id="filterBtn"><i class="fa fa-search pr-1"></i> Search</button>
                                </div>
                                <div class="col-md-6">
                                    <div class='row'>
                                        <div class="col-md-3 form-check mt-2">
                                            <input class="form-check-input" type="checkbox" value="" id="showDate">
                                            <label class="form-check-label" for="showDate">Show Date</label>
                                        </div>
                                        @if((Auth::user()->role == 'admin') || (Auth::user()->role == 'bdm' && $menu == 'My Requirements'))
                                            <div class="col-md-3 form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="" id="showMerge">
                                                <label class="form-check-label" for="showMerge">Show Merge</label>
                                            </div>
                                        @endif
                                        @if(Auth::user()->role == 'admin')
                                            <div class="col-md-3 form-check mt-2">
                                                <input class="form-check-input" type="checkbox" value="" id="showLink">
                                                <label class="form-check-label" for="showLink">Show Link</label>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-4">    
                                    <a href="{{ route('requirement.create') }}"><button class="btn btn-info float-right" type="button"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                                <div class="col-md-12 border mt-3 pb-3 pt-3 pl-3 pb-3 pr-3" id="filterDiv">
                                    {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                        @include('admin.'.$filterFile)
                                        {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <div id="overlay">
                                <div id="spinner"></div>
                            </div>
                            <table id="requirementTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr class="border  border-danger">
                                        <th>Daily #</th>
                                        <th>J Id</th>
                                        <th>Job Title</th>
                                        <th>BDM</th>
                                        <th>Duration</th>
                                        <th>Location</th>
                                        <th>Rate</th>
                                        <th>Onsite</th>
                                        <th>Category</th>
                                        <!-- <th>Timer</th> -->
                                        <th>Job Keyword</th>
                                        <th>client</th>
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
@endsection

@section('jquery')
<script type="text/javascript">
    $(document).ready(function () {
        datatables();
    });

    function showRequirementFilterData(){
        @if(Auth::user()->role == 'admin')
            var servedJobStatus = $('#served').val();
            if(servedJobStatus){
                var recruiter = $('#recruiter').val();
                if(!recruiter){
                    swal("Warning", "Please Select Recruiter From Filter.", "warning");
                    return;
                }
            }
        @endif
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function clearRequirementData(){
        $('#filterForm')[0].reset();
        $('select').trigger('change');
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function datatables(){
        var isShowMerge = 0;
        var progressBar = $('#progress-bar');
        if($('#showMerge').is(":checked")){
            isShowMerge = 1;
        }
        var table = $('#requirementTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            language: {
                loadingRecords: 'Processing...',
                processing: 'Loading...'
            },
            ajax: {
                url: "{{ $type == 1 ? route('requirement.index') : route('my_requirement') }}",
                data: function (d) {
                    var formDataArray = $('#filterForm').find(':input:not(select[multiple])').serializeArray();

                    // Filter out non-multiple-select elements and create a single array
                    var multipleSelectValues = $('#filterForm select[multiple]').map(function () {
                        return { name: $(this).attr('name'), value: $(this).val() };
                    }).get();

                    formDataArray = formDataArray.concat(multipleSelectValues);
                    console.log(formDataArray);
                    var formData = {};
                    $.each(formDataArray, function(i, field){
                        formData[field.name] = field.value;
                    });
                    d = $.extend(d, formData);
                    return d;
                },
            },
            columns: [
                {data: 'DT_RowIndex', 'width': '4%', name: 'DT_RowIndex', orderable: false, searchable: false, render: function(data, type, full, meta){
                    var columnData = data;
                    var objectDate = new Date(full['created_at']);
                    let day = objectDate.getDate();
                    // Added 1 in month as in javascript month range is 0-11
                    let month = objectDate.getMonth() + 1;
                    let year = objectDate.getFullYear().toString().substr(-2);
                    columnData += '<p>'+ month +'/'+ day +'/'+ year +'</p>';
                    return columnData;
                }},
                {data: 'job_id', 'width': '8%', name: 'job_id'},
                {data: 'job_title', 'width': '30%', name: 'job_title'},
                {data: 'user_id', 'width': '6%', name: 'user_id'},
                {data: 'duration', name: 'duration'},
                {data: 'location', name: 'location'},
                {data: 'my_rate', name: 'my_rate'},
                {data: 'work_type', name: 'work_type'},
                {data: 'category', name: 'category'},
                // {data: 'created_at', 'width': '18%', name: 'created_at'},
                {data: 'job_keyword', 'width': '20%', name: 'job_keyword'},
                {data: 'client', name: 'client'},
                {data: 'recruiter', name: 'recruiter'},
                {data: 'status', 'width': '20%', name: 'status'},
                // {data: 'color', name: 'color'},
                {data: 'candidate', name: 'candidate'},
                {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
            ],
            drawCallback: function() {
                if($('#showMerge').is(':checked')){
                    $('#requirementTable tbody tr').each(function(trIndex) {
                        var currentTr = this;
                        var rowType = '';
                        if($(this).hasClass('parent-row')){
                            rowType = 'parant-row';
                        }
                        if($(this).hasClass('child-row')){
                            rowType = 'child-row';
                        }
                        if($.trim(rowType) != ''){
                            $(this).find('td').each(function(tdIndex){
                                $(this).addClass('color-group');
                                if(rowType == 'parant-row'){
                                    $(this).addClass('border-bottom');
                                }
                                if(rowType == 'child-row'){
                                    if(trIndex == 0){
                                        $(this).addClass('border-top');
                                    }
                                }
                                if (tdIndex === 0) {
                                    $(this).addClass('border-left');
                                }
                                if (tdIndex === $(this).siblings().length) {
                                    $(this).addClass('border-right');
                                }
                            })
                        }   
                    });
                }
            },
        });

        $('#requirementTable').on('preXhr.dt', function () {
            $('#overlay').show();
        });

        $('#requirementTable').on('xhr.dt', function () {
            $('#overlay').hide();
        });
    }

    $(function () {
        $('#requirementTable tbody').on('click', '.deleteRequirement', function (event) {
            event.preventDefault();
            var roleId = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "You want to delete this requirement?",
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
                        url: "{{url('admin/requirement')}}/"+roleId,
                        type: "DELETE",
                        data: {_token: '{{csrf_token()}}' },
                        success: function(data){
                            //table.row('.selected').remove().draw(false);
                            $('#requirementTable').DataTable().ajax.reload();
                            swal("Deleted", "Your data successfully deleted!", "success");
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });

        $('#requirementTable tbody').on('click', '.assign', function (event) {
            event.preventDefault();
            var user_id = $(this).attr('uid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': user_id},
                headers: { 'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+user_id).show();
                    $('#assign_add_'+user_id).hide();
                    $('#requirementTable').DataTable().ajax.reload();
                    //table.draw(false);
                    //datatables();
                }
            });
        });

        $('#requirementTable tbody').on('click', '.unassign', function (event) {
            event.preventDefault();
            var user_id = $(this).attr('ruid');
            var url = $(this).attr('url');
            var l = Ladda.create(this);
            l.start();
            $.ajax({
                url: url,
                type: "post",
                data: {'id': user_id,'_token' : $('meta[name=_token]').attr('content') },
                success: function(data){
                    l.stop();
                    $('#assign_remove_'+user_id).hide();
                    $('#assign_add_'+user_id).show();
                    $('#requirementTable').DataTable().ajax.reload();
                    //table.draw(false);
                    //datatables();
                }
            });
        });

        $('#requirementTable tbody').on('click', '.noChange', function (event) {
            swal("Cancelled", "You can not change the status", "error");
        });

        $('#showMerge').click(function(){
            $("#requirementTable").dataTable().fnDestroy();
            datatables();
        })

        $('#showLink').click(function(){
            if($('#showLink').is(':checked')){
                $(".link-data").show();
            }else{
                $(".link-data").hide();
            }
        });
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
