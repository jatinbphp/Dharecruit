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
                        <h3 class="card-title">{{ ucfirst($menu) }} Data</h3>
                        <a href="{{ route('moi.create') }}" title="Add Users" class="btn btn-primary" style="float:right;"><i class="fa fa-plus"></i></a>
                    </div>
                    <div class="card-body" id="returnsData">
                        <table id="mois_datatable" class="table table-bordered table-striped table-re">
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
        var table = $('#mois_datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('moi.index') }}",
            columns: [
                {data: 'name', name: 'name'},
                {data: 'action', "width": "12%", name: 'action', orderable: false, searchable: false},
            ]
        });

       $('#mois_datatable tbody').on('click', '.delete_moi', function (event) {
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
                        url: "{{url('admin/moi')}}/" + $(this).attr("data-id"),
                        data: {
                            _token: '{{csrf_token()}}'
                        },
                        success: function (data) {
                            $('#mois_datatable').DataTable().ajax.reload();
                            Swal.fire('Deleted!', 'moi has been deleted.', 'success')
                        }
                    });
                }
            });
        });
    });
</script>
@endsection