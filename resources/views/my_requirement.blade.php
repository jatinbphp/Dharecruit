@extends('layouts.app')
@section('css')
@endsection
@section('content')
<div class="content-header">
    <div class="container-fluid">
        @include('flash.flash-message')
        <div class="row mb-2">
            <div class="col-sm-12">
                <h1 class="m-0">{{ ucfirst($menu) }}</h1>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ ucfirst($menu) }} Data</h3>
                        <a href="{{ route('admin.create-requirement') }}" title="Add Users" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i></a>
                    </div>
                    <div class="card-body" id="returnsData">
                        <table id="requirements_datatable" class="table table-bordered table-striped table-re">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Experience</th>
                                    <th>Location</th>
                                    <th>Type</th>
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
    </div>
</section>
@endsection
@section('js')

<script type="text/javascript">
    $(function () {
        var table = $('#requirements_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.my-requirement') }}",
            columns: [
                {data: 'job_title', name: 'job_title'},
                {data: 'experience', name: 'experience'},
                {data: 'locations', name: 'locations'},
                {data: 'work_type', name: 'work_type'},
                {data: 'action', "width": "12%", name: 'action', orderable: false, searchable: false},
            ]
        });

       $('#requirements_datatable tbody').on('click', '.delete_requirement', function (event) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type:"GET",
                        url: "{{url('admin/destroy-requirements')}}/" + $(this).attr("data-id"),
                        data: {
                            _token: '{{csrf_token()}}'
                        },
                        success: function (data) {
                            $('#requirements_datatable').DataTable().ajax.reload();
                            Swal.fire('Deleted!', 'Record deleted successfully.', 'success')
                        }
                    });
                }
            });
        });
    });
</script>
@endsection