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
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</div>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <!-- small box -->
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Categories Data</h3>
                        <a href="{{ route('category.create') }}" title="Add Users" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i></a>
                    </div>
                    <div class="card-body" id="returnsData">
                        <table id="categories_datatable" class="table table-bordered table-striped table-re">
                            <thead>
                                <tr>
                                    <th>Name</th>
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
        var table = $('#categories_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('category.index') }}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'action', "width": "12%", name: 'action', orderable: false, searchable: false},
            ]
        });

       $('#categories_datatable tbody').on('click', '.delete_category', function (event) {
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
                        type: "DELETE",
                        url: "{{url('admin/category')}}/" + $(this).attr("data-id"),
                        data: {
                            _token: '{{csrf_token()}}'
                        },
                        success: function (data) {
                            //$("#" + $(this).attr("data-id")).remove();
                            location.reload();
                            Swal.fire('Deleted!', 'category has been deleted.', 'success')
                        }
                    });
                }
            });
        });
    });
</script>
@endsection