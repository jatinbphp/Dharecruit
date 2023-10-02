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
                                    <!-- <a href="{{ route('orders.export') }}"><button class="btn btn-warning float-right" type="button" style="margin-right: 1.5%;"><i class="fa fa-file-export pr-1"></i> Export to CSV</button></a> -->
                                    <a href="{{ route('orders.create') }}"><button class="btn btn-info float-right" type="button" style="margin-right: 1.5%;"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                            </div>
                        </div>

                        <div class="card-header">
                            <div class="row">
                                <div class="col-md-12">
                                    <h4 for="status" style="border-bottom: 3px solid #17a2b8;" class="form-label w-100">Filters :</h4>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <label for="status" class="form-label">Status :</label>
                                    <select id="status" onChange="showData();" class="form-control">
                                        <option value="">Select</option>
                                        @foreach (\App\Models\Orders::$status as $key => $value)
                                            <option value="{{$value}}">{{$value}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label for="customer" class="form-label">Customer :</label>
                                    <input type="text" id="customer" onKeyup="showData();" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="start_date" class="form-label">Start Date :</label>
                                    <input type="date" id="start_date" onChange="showData();" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label for="end_date" class="form-label">End Date :</label>
                                    <input type="date" id="end_date" onChange="showData();" class="form-control">
                                </div>
                            </div>
                        </div>

                        <div class="card-body table-responsive">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table id="ordersTable" class="table table-bordered display nowrap" width="100%">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Customer Information</th>
                                                <th>Cellphone</th>
                                                <th>Products</th>
                                                <th style="width: 12%;">Status</th>
                                                <th style="width: 12%;">Total</th>
                                                <th style="width: 18%;" >Delivery Date</th>
                                                <!-- <th style="width: 18%;" >Fullfillment Date</th> -->
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
            </div>
        </section>
    </div>
@endsection

@section('jquery')
<script type="text/javascript">
jQuery(document).ready(function () {
    datatables();
});

function showData(){
    $("#ordersTable").dataTable().fnDestroy();
    datatables();
}

function datatables(){
    var ajaxUrl = "{{ route('orders.index') }}"

    @if(isset($customer_name) && !empty($customer_name))
        var customer_name = '{{$customer_name['name']}}';
    @else
        var customer_name = $('#customer').val();
    @endif

    var table = $('#ordersTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        buttons: [
            {
                text: 'csv',
                extend: 'csvHtml5',
            },
        ],
        ajax: {
            url: ajaxUrl,
            data: function (d) {
                d.status = $('#status').val();
                d.customer = customer_name;
                d.start_date = $('#start_date').val();
                d.end_date = $('#end_date').val();
                d._token = '{{ csrf_token() }}';
            }
        },
        columns: [
            {data: 'unique_id', name: 'unique_id'},
            {data: 'customer_name', name: 'customer_name', orderable: false},
            {data: 'customer_phone', name: 'customer_phone', orderable: false},
            {data: 'order_items', name: 'order_items', orderable: false},
            {data: 'status', name: 'status', orderable: false},
            {data: 'order_total', name: 'order_total'},
            {data: 'order_date', name: 'order_date'},
            /*{data: 'order_date', name: 'order_date'},*/
            {data: 'action', "width": "15%", name: 'action', orderable: false, searchable: false},
        ]
    });
}

$(function () {
    $('#ordersTable tbody').on('click', '.deleteOrder', function (event) {
        event.preventDefault();
        var cId = $(this).attr("data-id");
        swal({
            title: "Are you sure?",
            text: "To delete this order",
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
                    url: "{{url('admin/orders')}}/"+cId,
                    type: "DELETE",
                    data: {_token: '{{csrf_token()}}' },
                    success: function(data){
                        $("#ordersTable").dataTable().fnDestroy();
                        datatables();
                        swal("Deleted", "Your data successfully deleted!", "success");
                    }
                });
            } else {
                swal("Cancelled", "Your data safe!", "error");
            }
        });
    });

    $('#ordersTable tbody').on('click', '.assign', function (event) {
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
                table.draw(false);
            }
        });
    });

    $('#ordersTable tbody').on('click', '.unassign', function (event) {
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
                table.draw(false);
            }
        });
    });

    $('#ordersTable tbody').on('change', '.orderStatus', function (event) {
        event.preventDefault();
        var order_id = $(this).attr('data-id');
        var status = $(this).val();
        swal({
            title: "Are you sure?",
            text: "To update status of this order",
            type: "warning",
            showCancelButton: true,
            confirmButtonColor: '#17a2b8',
            confirmButtonText: 'Yes, Sure',
            cancelButtonText: "No, cancel",
            closeOnConfirm: false,
            closeOnCancel: false
        },
        function(isConfirm) {
            if (isConfirm) {
                $.ajax({
                    url: "{{route('orders.status')}}",
                    type: "post",
                    data: {'id': order_id, status:status, '_token' : $('meta[name=_token]').attr('content') },
                    success: function(data){
                        if(data == 1){
                            $("#ordersTable").dataTable().fnDestroy();
                            datatables();
                            swal("Success", "Order status is updated", "success");
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
</script>
@endsection
