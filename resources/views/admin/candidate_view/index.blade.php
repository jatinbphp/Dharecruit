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
                                                                 <span class="h5" onclick="editViewLimit('{{$userId}}')" id="{{$userId}}">Set Candidate View Limt</span>
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
        function editViewLimit(teamId){
            var currentLimit = $("#limit_"+teamId).text();
            var inputField = $('<input>').attr({
                type: 'number',
                id: 'nameInput_'+teamId,
                style: 'width:80px',
                value: currentLimit
            });
            var buttonsHtml = '<i class="fas fa-check-circle text-success ml-1" id="save-team-name-'+teamId+'" onclick="saveTeamName('+teamId+')"></i>'+
                '<i class="fas fa-times-circle text-danger ml-1" id="cancel-team-name-'+teamId+'" onclick="cancelButton('+teamId+')"></i>';
            $('#button-group-'+teamId).append(buttonsHtml);
            $('#'+teamId).hide();
            $("#team-name-label-"+teamId).hide();
            $("#tean_name_"+teamId).append(inputField);
        }
    </script>
@endsection
