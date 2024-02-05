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
                            {{-- <div class="row">
                                <div class="col-md-12">
                                    <a href="{{ route('employee.create') }}"><button class="btn btn-info float-right" type="button" ><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                            </div> --}}
                        </div>
                        <div class="card-body table-responsive">
                            <table id="candidateTable" class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Candidate Id</th>
                                        <th>Candidate Name</th>
                                        <th>Candidate Email</th>
                                        <th>Candidate Phone</th>
                                        <th style="width: 12.5%;">Action</th>
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
    $(function () {
        var table = $('#candidateTable').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 100,
            lengthMenu: [ 100, 200, 300, 400, 500 ],
            ajax: "{{ route('manage_candidate.index') }}",
            order: [[1, 'desc']],
            columns: [
                {data: 'id', name: 'id'},
                {data: 'candidate_id', name: 'candidate_id'},
                {data: 'name', name: 'name'},
                {data: 'email', name: 'email'},
                {data: 'phone', name: 'phone'},
                {data: 'action', "width": "12.5%", name: 'action', orderable: false, searchable: false},
            ]
        });
    });
  </script>
@endsection
