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
                                    <a href="{{ route('manage_team.create') }}"><button class="btn btn-info float-right" type="button"><i class="fa fa-plus pr-1"></i> Add New</button></a>
                                </div>
                            </div>
                        </div>
                        <div id="teamMembers">
                            @include('admin.team.teamData')
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal fade" id="updateTeamLeadModel" tabindex="-1" role="dialog" aria-labelledby="updateTeamLeadModelLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateTeamLeadModelLabel">Update Team Lead</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="team_lead_update_id">
                    <select id="remainingUsers" class="form-control select2" style='width:100%;'>
                        <option value="" selected>Please Select Team Lead</option>
                    </select>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-warning" onclick="updateTeamLead()">Update Team Lead</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('jquery')
    <script type="text/javascript">
        function updateMember(userId, teamId) {
            if(!userId || !teamId){
                return;
            }

            var type = 'remove';
            if($('#'+teamId+'_'+userId).is(':checked')){
                type = 'update';
            }

            swal({
                title: "Are you sure?",
                text: "You want Update This Group.",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: '#f8bb86',
                confirmButtonText: 'Yes, Update',
                cancelButtonText: "No, cancel",
                closeOnConfirm: false,
                closeOnCancel: false
            },
            function(isConfirm) {
                if (isConfirm) {
                    $.ajax({
                        url: "{{ route('update_user_team') }}",
                        method: 'POST',
                        data: {
                            '_token'  : '{{ csrf_token() }}',
                            'user_id' : userId,
                            'team_id' : teamId,
                            'type'    : type,
                        },
                        success: function(response) {
                            $('#teamMembers').html(response.html);
                            if(response.status == 1){
                                swal("Success", "Team Updated Successfully!", "success");
                            } else {
                                swal("Error", "Something Went Wrong!", "error");
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching data:', error);
                        }
                    });
                } else {
                    if(type == 'update'){
                        $('#'+teamId+'_'+userId).prop("checked", false);
                    } else {
                        $('#'+teamId+'_'+userId).prop("checked", true);
                    }
                    swal("Cancelled", "Your data safe!", "error");
                }
            });
        }

        function editTeamName(teamId){
            var currentName = $("#tean_name_"+teamId).text();
            var inputField = $('<input>').attr({
                type: 'text',
                id: 'nameInput_'+teamId,
                style: 'width:80px',
                value: currentName
            });
            var buttonsHtml = '<i class="fas fa-check-circle text-success ml-1" id="save-team-name-'+teamId+'" onclick="saveTeamName('+teamId+')"></i>'+
                '<i class="fas fa-times-circle text-danger ml-1" id="cancel-team-name-'+teamId+'" onclick="cancelButton('+teamId+')"></i>';
            $('#button-group-'+teamId).append(buttonsHtml);
            $('#'+teamId).hide();
            $("#team-name-label-"+teamId).hide();
            $("#tean_name_"+teamId).append(inputField);
        }

        function cancelButton(teamId){
            $('#save-team-name-'+teamId).remove();
            $('#cancel-team-name-'+teamId).remove();
            $('#nameInput_'+teamId).remove();
            $('#'+teamId).show();
            $("#team-name-label-"+teamId).show();
            var text = $('#nameInput_'+teamId).text();
        }

        function saveTeamName(teamId){
            var teamName = $('#nameInput_'+teamId).val();
            if(!teamId || !teamName){
                return;
            }
            $.ajax({
                url: "{{ route('update_team_name') }}",
                method: 'POST',
                data: {
                    '_token'    : '{{ csrf_token() }}',
                    'team_id'   : teamId,
                    'team_name' : teamName,
                },
                success: function(response) {
                    if(response.status == 2){
                        swal("Warning", teamName+" Already Exists!", "warning");
                        return;
                    }
                    $('#teamMembers').html(response.html);
                    if(response.status == 1){
                        swal("Success", "Team Updated Successfully!", "success");
                    } else {
                        swal("Error", "Something Went Wrong!", "error");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        function editTeamLeader(teamId, teamType, teamLeadId){
            if(!teamId || !teamType || !teamLeadId){
                return;
            }
            $.ajax({
                url: "{{ route('update_team_lead_name') }}",
                method: 'POST',
                data: {
                    '_token'       : '{{ csrf_token() }}',
                    'team_id'      : teamId,
                    'team_lead_id' : teamLeadId,
                    'team_type'    : teamType,
                },
                success: function(response) {
                    if(response.status == 1){
                        $('#remainingUsers').select2({
                            data: Object.keys(response.user_data).map(function(key) {
                                return { id: key, text: response.user_data[key] };
                            }),
                        });
                        $('#remainingUsers').select2();
                        $('#updateTeamLeadModel').modal('show');
                        $('#team_lead_update_id').val(teamId);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }

        function updateTeamLead(){
            var teamLeadId = $('#remainingUsers').select().val();
            var teamId = $('#team_lead_update_id').val();

            if(!teamLeadId || !teamId){
                return;
            }

            $.ajax({
                url: "{{ route('update_team_lead') }}",
                method: 'POST',
                data: {
                    '_token'       : '{{ csrf_token() }}',
                    'team_id'      : teamId,
                    'team_lead_id' : teamLeadId,
                },
                success: function(response) {
                    $('#updateTeamLeadModel').modal('hide');
                    $('#teamMembers').html(response.html);
                    if(response.status == 1){
                        swal("Success", "Team Lead Updated Successfully!", "success");
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
        }
    </script>
@endsection
