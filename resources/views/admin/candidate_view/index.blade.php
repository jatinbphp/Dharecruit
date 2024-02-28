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
                                <div class="col-4">
                                    <h5>Set Global View Limit:</h5>
                                    <input type="number" class="form-control float-left" id="global-view-limit" style="width: 75%" placeholder="Select Global View Limit" value="{{$globalValue}}">
                                    <button class="btn btn-success ml-2" type="button" onclick="saveRecruiterGlobalLimt()"><i class="fas fa-check-circle ml-1"></i></button>
                                </div>
                            </div>
                        </div>
                        <div class="card-header">
                            <div class="row">
                                @foreach($allRecruiter as $userId => $recruiter)
                                    <div class="col-md-3">
                                        <div class="card card-widget widget-user-2 shadow-sm">
                                            <div class="widget-user-header bg-info" style="height: 100px;">
                                                <div class="widget-user-image">
                                                    <img class="img-circle elevation-2" src="{{url('assets/dist/img/recruiter.png')}}" alt="User Avatar">
                                                </div>
                                                <h3 class="widget-user-username">{{$recruiter}}</h3>
                                                <h5 class="widget-user-desc">{{isset($userWiseTeam[$userId]) ? $userWiseTeam[$userId] : ''}}</h5>
                                            </div>
                                            <div class="card-footer p-0">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item">
                                                        <a href="#" class="nav-link">
                                                            <span id="recruiter_label_{{$userId}}">
                                                                <span class="h5" onclick="editViewLimit('{{$userId}}')" id="{{$userId}}">Set Candidate View Limt</span>
                                                            </span><span id="button-group-{{$userId}}">
                                                            <span class="float-right badge bg-warning p-2 mb-2" style="font-size: 15px;" id="limit_{{$userId}}">{{isset($userWiseLimit[$userId]) ? $userWiseLimit[$userId] : ''}}</span>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
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
        function editViewLimit(recruiterId){
            var currentLimit = $("#limit_"+recruiterId).text();
            var inputField = $('<input>').attr({
                type: 'number',
                id: 'nameInput_'+recruiterId,
                style: 'width:80px',
                value: currentLimit
            });
            var buttonsHtml = '<i class="fas fa-check-circle text-success ml-1" id="save-candidate-limit-'+recruiterId+'" onclick="saveRecruiterLimt('+recruiterId+')"></i>'+
                '<i class="fas fa-times-circle text-danger ml-1" id="cancel-team-name-'+recruiterId+'" onclick="cancelButton('+recruiterId+')"></i>';
            $('#button-group-'+recruiterId).append(buttonsHtml);
            $('#'+recruiterId).hide();
            $("#recruiter_label_"+recruiterId).append(inputField);
        }

        function cancelButton(recruiterId){
            $('#save-candidate-limit-'+recruiterId).remove();
            $('#cancel-team-name-'+recruiterId).remove();
            $('#nameInput_'+recruiterId).remove();
            $('#'+recruiterId).show();
            $("#team-name-label-"+recruiterId).show();
            var text = $('#nameInput_'+recruiterId).text();
        }

        function saveRecruiterLimt(recruiterId){
            var limit = $('#nameInput_'+recruiterId).val();
            if(!recruiterId || !limit){
                return;
            }
            $.ajax({
                url: "{{ route('update_recruiter_limit') }}",
                method: 'POST',
                data: {
                    '_token'       : '{{ csrf_token() }}',
                    'recruiter_id' : recruiterId,
                    'limit'        : limit,
                },
                success: function(response) {
                    if(response.status == 1){
                        swal("Success", "Limit Updated Successfully!", "success");
                        $('#limit_'+recruiterId).text(limit);
                        cancelButton(recruiterId);
                    } else {
                        swal("Error", "Something Went Wrong!", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        function saveRecruiterGlobalLimt(){
            var limit = $('#global-view-limit').val();
            if(!limit){
                return;
            }
            $.ajax({
                url: "{{ route('update_recruiter_global_limit') }}",
                method: 'POST',
                data: {
                    '_token'       : '{{ csrf_token() }}',
                    'limit'        : limit,
                },
                success: function(response) {
                    if(response.status == 1){
                        swal("Success", "Limit Updated Successfully!", "success");
                        $('#global-view-limit').val(limit);
                    } else {
                        swal("Error", "Something Went Wrong!", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }
    </script>
@endsection
