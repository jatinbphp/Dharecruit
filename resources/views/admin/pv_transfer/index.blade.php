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
                            <div class="table-responsive mt-2">
                                <table class="table table-bordered table-striped" id="poc_transfer">
                                    <thead>
                                        @if(isset($headings) && count($headings))
                                            <tr>
                                                @foreach($headings as $headindg)
                                                    <th scope="col" class="text-center element-border">{{$headindg}}</th>
                                                @endforeach
                                            </tr>
                                        @endif
                                    </thead>
                                    <tbody>
                                        @if(isset($pvCompaniesData) && count($pvCompaniesData))
                                            @foreach($pvCompaniesData as $companyId => $pvCompanyData)
                                                @if(count($pvCompanyData))
                                                    <tr>
                                                        @foreach($pvCompanyData as $key => $data)
                                                            @if($key == 'transfer_poc')
                                                                <td onclick="transferPoc('{{$companyId}}')" class="element-border text-center"><i class="fa fa-exchange-alt"></i></td>
                                                            @else
                                                                <td class="element-border">{{$data}}</td>
                                                            @endif
                                                        @endforeach
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            <tr><td colspan="12" class="text-center">No Records Found.</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    @include('admin.pv_transfer.transferPocModel')
@endsection
@section('jquery')
    <script type="text/javascript">
        function transferPoc(id){
            $.ajax({
                url: "{{route('pv_transfer.getPvCompanyData')}}",
                type: "POST",
                data: {'id': id ,_token: '{{csrf_token()}}' },
                success: function(data){
                    if(data.status == 1){
                        $('#pv_company_name').text(data.pv_company_data.name).attr('data-company-id',data.pv_company_data.id);
                        $('#poc_name').text(data.pv_company_data.poc_name);
                        $('#poc_email').text(data.pv_company_data.email);
                        $('#poc_phone').text(data.pv_company_data.phone);
                    }
                }
            });
            $("#transferPoc").modal('show');
        }
        $('#bdm').on('change', function () {
            var userId = $(this).val();
            var companyId =  $('#pv_company_name').attr('data-company-id');
            var pocName = $('#poc_name').val();
            var bdmName = $(this).find(':selected').text();

            if(!userId || !companyId){
                swal("Error", "Something is wrong!", "error");
                return;
            }

            swal({
                title: "Are you sure?",
                text: "You want to Transfer this" +pocName+ " POC To "+ bdmName +".",
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
                        url: "{{route('pv_transfer.transferPoc')}}",
                        type: "POST",
                        data: {'user_id': userId, 'company_id': companyId ,_token: '{{csrf_token()}}' },
                        success: function(data){
                            if(data.status == 1){
                                $("#transferPoc").modal('hide');
                                swal({
                                    title: "Success",
                                    text: pocName +" POC Successfully  Transferred To "+ bdmName +".",
                                    type: "success",
                                    showCancelButton: false,
                                    confirmButtonColor: '#138496',
                                    confirmButtonText: 'Ok',
                                    closeOnConfirm: false,
                                },
                                function(isConfirm) {
                                    if (isConfirm) {
                                        window.location.reload();
                                    }
                                });
                            }
                        }
                    });
                } else {
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        });
    </script>
@endsection
