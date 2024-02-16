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
                        <div class="callout callout-info">
                            <h4><i class="fa fa-users-cog"></i> BDM Teams:</h4>
                            <div class="row m-2">
                                @if($teamBdmData->count())
                                    @foreach($teamBdmData as $team)
                                        <div class="col-4">
                                            <div class="card m-3 card-outline" style="max-width: 40rem; border-top: 3px solid {{$team->team_color}};">
                                                <div class="card-header">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        <span>Team Name: <span style="color: {{$team->team_color}}">{{$team->team_name}}</span></span>
                                                        <span>Lead Name: <span class="text-left" style="color: {{$team->team_color}}">{{$team->TeanLead->name}}</span></span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <div class="row"></div>
                                                    <h5 class="card-title mb-2">Select Team Manager:</h5>
                                                    <div class="card-text">
                                                        <div class="row">
                                                            <select class="form-control select2" id="team_{{$team->id}}" onchange="editManager('{{$team->id}}')">
                                                                @foreach($allBdmData as $userId => $bdm)
                                                                    <option value="{{$userId}}" @if($team->manager_id == $userId) selected @endif>{{$bdm}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 mt-3 alert alert-warning" role="alert">
                                        No BDM Team Found.
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="callout callout-info">
                            <h4><i class="fa fa-users-cog"></i> Recruiter Team:</h4>
                            <div class="row m-2"    >
                                @if($teamRecData->count())
                                    @foreach($teamRecData as $team)
                                        <div class="col-4">
                                            <div class="card card-outline m-3" style="max-width: 40rem; border-top: 3px solid {{$team->team_color}};">
                                                <div class="card-header">
                                                    <div style="display: flex; justify-content: space-between;">
                                                        <span>Team Name: <span style="color: {{$team->team_color}}">{{$team->team_name}}</span></span>
                                                        <span>Lead Name: <span class="text-left" style="color: {{$team->team_color}}">{{$team->TeanLead->name}}</span></span>
                                                    </div>
                                                </div>
                                                <div class="card-body">
                                                    <h5 class="card-title mb-2">Select Team Manager:</h5>
                                                    <div class="card-text">
                                                        <div class="row">
                                                            <select class="form-control select2" id="team_{{$team->id}}" onchange="editManager('{{$team->id}}')">
                                                                @foreach($allRecData as $userId => $recruiter)
                                                                    <option value="{{$userId}}" @if($team->manager_id == $userId) selected @endif>{{$recruiter}}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12 mt-3 alert alert-warning" role="alert">
                                        No Recruiter Team Found.
                                    </div>
                                @endif
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
        $(function () {
            $('.select2').select2();
        });

        function editManager(teamId){
            var userId = $('#team_'+teamId).select().val();
            if(!teamId || !userId){
                return;
            }
            $.ajax({
                url: "{{ route('update_team_manager') }}",
                method: 'POST',
                data: {
                    '_token'   : '{{ csrf_token() }}',
                    'team_id' : teamId,
                    'user_id'  : userId,
                },
                success: function(response) {
                    $('#teamMembers').html(response.html);
                    $('#teamMembers').html(response.html);
                    $('#teamMembers').html(response.html);
                    if(response.status == 1){
                        swal("Success", "Team Manager Updated Successfully!", "success");
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
