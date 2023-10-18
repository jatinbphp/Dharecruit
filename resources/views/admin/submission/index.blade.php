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
                                    {!! Form::open(['id' => 'filterForm', 'class' => 'form-horizontal','files'=>true,'onsubmit' => 'return false;']) !!}
                                    @include('admin.filter')
                                    {!! Form::close() !!}
                                </div>
                            </div>
                        </div>
                        <div class="card-body table-responsive">
                            <table id="requirementTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Daily #</th>
                                        <th>Job Id</th>
                                        <th>Job Title</th>
                                        <th>BDM</th>
                                        <th>Duration</th>
                                        <th>Location</th>
                                        <th>Rate</th>
                                        <th>Onsite</th>
                                        <th>Category</th>
                                        <th>Timer</th>
                                        <th>Job Keyword</th>
                                        <th>client</th>
                                        <th>Recruiter</th>
                                        <th>Status</th>
                                        <th>Color</th>
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
@endsection

@section('jquery')
<script type="text/javascript">
    $(document).ready(function () {
        datatables();
    });

    function showData(){
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function clearData(){
        $('#filterForm')[0].reset();
        $("#requirementTable").dataTable().fnDestroy();
        datatables();
    }

    function datatables() {
        var table = $('#requirementTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: "{{ $type == 1 ? route('submission.index') : route('my_submission') }}",
                data: function (d) {
                    d.date = $('#reqDate').val();
                    d.requirement = $('#requirement').val();
                    d.bdm = $('#bdm').val();
                    d.recruiter = $('#recruiter').val();
                    d.poc_email = $('#poc_email').val();
                    d.pv_company = $('#pv_company').val();
                    d.moi = $('#moi').val();
                    d.work_type = $('#work_type').val();
                    d._token = '{{ csrf_token() }}';
                }
            },
            columns: [
                {data: 'DT_RowIndex', 'width': '6%', name: 'DT_RowIndex', orderable: false, searchable: false },
                {data: 'job_id', 'width': '8%', name: 'job_id'},
                {data: 'job_title', 'width': '30%', name: 'job_title'},
                {data: 'user_id', 'width': '6%', name: 'user_id'},
                {data: 'duration', name: 'duration'},
                {data: 'location', name: 'location'},
                {data: 'vendor_rate', name: 'vendor_rate'},
                {data: 'work_type', name: 'work_type'},
                {data: 'category', name: 'category'},
                {data: 'created_at', 'width': '18%', name: 'created_at'},
                {data: 'job_keyword', 'width': '20%', name: 'job_keyword'},
                {data: 'client', name: 'client'},
                {data: 'recruiter', name: 'recruiter'},
                {data: 'status', name: 'status'},
                {data: 'color', name: 'color'},
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
                            console.log(data);
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