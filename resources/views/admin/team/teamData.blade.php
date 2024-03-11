<div class="callout callout-info">
    <h4><i class="fa fa-users-cog"></i> BDM Teams:</h4>
    <div class="row m-2">
        @if($teamBdmData->count())
            @foreach($teamBdmData as $team)
            <div class="col-4">
                <div class="card m-3 card-outline" style="max-width: 40rem; border-top: 3px solid {{$team->team_color}};">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Team Name: <span id="tean_name_{{$team->id}}" style="color: {{$team->team_color}}" class="font-weight-bold"><span id="team-name-label-{{$team->id}}">{{$team->team_name}}</span></span><span id="button-group-{{$team->id}}"><i class="fas fa-edit ml-1" id="{{$team->id}}" onclick="editTeamName('{{$team->id}}')" data-toggle="tooltip" title="Edit Team Name" data-trigger="hover"></i></span></span>
                            <span>Lead Name: <span class="text-left font-weight-bold" style="color: {{$team->team_color}}">{{$team->TeanLead->name}}</span><i class="fas fa-edit ml-1" id="{{$team->id}}" onclick="editTeamLeader('{{$team->id}}','{{$team->team_type}}', '{{$team->team_lead_id}}')" data-toggle="tooltip" title="Edit Team Leader" data-trigger="hover"></i></span>
                        </div>
                        <div class="mt-2">
                            <span>Manager Name: <span class="font-weight-bold">{{isset($allBdmUsers[$team->manager_id]) ? $allBdmUsers[$team->manager_id] : ''}}</span></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row"></div>
                        <h5 class="card-title mb-2">Team Members:</h5>
                        <div class="card-text">
                            <div class="row">
                                @if($allBdmUsers)
                                    @foreach($allBdmUsers as $id => $user)
                                        @if($id == $team->team_lead_id)
                                            @continue
                                        @endif
                                        @php $activeMembers = isset($teamWiseData[$team->id]) ? $teamWiseData[$team->id] : [] @endphp
                                        @if(!in_array($id, $activeMembers) && in_array($id, array_merge(...$teamWiseData)) || in_array($id, $allLeadData))
                                            @continue
                                        @endif
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input user-checkbox" onChange="updateMember('{{$id}}','{{$team->id}}')" type="checkbox" id="{{$team->id}}_{{$id}}" @if(in_array($id, $activeMembers)) checked @elseif(in_array($id, array_merge(...$teamWiseData)) || in_array($id, $allLeadData)) disabled @endif>
                                                <label for="{{$team->id}}_{{$id}}" class="custom-control-label" style="color: @if(in_array($id, $activeMembers))  {{$team->team_color}} @endif">{{$user}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-md-12 text-right">
                                        <span><i class="fas fa-trash-alt" onclick="removeTeam('{{$team->id}}')" data-toggle="tooltip" title="Remove Team" data-trigger="hover"></i></span>
                                    </div>
                                @endif
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
    <h4><i class="fa fa-users-cog"></i> Recruiter Teams:</h4>
    <div class="row m-2"    >
        @if($teamRecData->count())
            @foreach($teamRecData as $team)
            <div class="col-4">
                <div class="card card-outline m-3" style="max-width: 40rem; border-top: 3px solid {{$team->team_color}};">
                    <div class="card-header">
                        <div style="display: flex; justify-content: space-between;">
                            <span>Team Name: <span id="tean_name_{{$team->id}}" style="color: {{$team->team_color}}" class="font-weight-bold"><span id="team-name-label-{{$team->id}}">{{$team->team_name}}</span></span><span id="button-group-{{$team->id}}"><i class="fas fa-edit ml-1" id="{{$team->id}}" onclick="editTeamName('{{$team->id}}')" data-toggle="tooltip" title="Edit Team Name" data-trigger="hover"></i></span></span>
                            <span>Lead Name: <span class="text-left font-weight-bold" style="color: {{$team->team_color}}">{{$team->TeanLead->name}}</span><i class="fas fa-edit ml-1" id="{{$team->id}}" onclick="editTeamLeader('{{$team->id}}','{{$team->team_type}}', '{{$team->team_lead_id}}')" data-toggle="tooltip" title="Edit Team Leader" data-trigger="hover"></i></span>
                        </div>
                        <div class="mt-2">
                            <span>Manager Name: <span class="font-weight-bold">{{isset($allRecUsers[$team->manager_id]) ? $allRecUsers[$team->manager_id] : ''}}</span></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h5 class="card-title mb-2">Team Members:</h5>
                        <div class="card-text">
                            <div class="row">
                                @if($allRecUsers)
                                    @foreach($allRecUsers as $id => $user)
                                        @if($id == $team->team_lead_id)
                                            @continue
                                        @endif
                                        @php $activeMembers = isset($teamWiseData[$team->id]) ? $teamWiseData[$team->id] : [] @endphp
                                        @if(!in_array($id, $activeMembers) && in_array($id, array_merge(...$teamWiseData)) || in_array($id, $allLeadData))
                                            @continue
                                        @endif
                                        <div class="col-6">
                                            <div class="custom-control custom-checkbox">
                                                <input class="custom-control-input user-checkbox" onChange="updateMember('{{$id}}','{{$team->id}}')" type="checkbox" id="{{$team->id}}_{{$id}}" @if(in_array($id, $activeMembers)) checked @endif>
                                                <label for="{{$team->id}}_{{$id}}" class="custom-control-label" style="color: @if(in_array($id, $activeMembers))  {{$team->team_color}} @endif">{{$user}}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                    <div class="col-md-12 text-right">
                                        <span><i class="fas fa-trash-alt" onclick="removeTeam('{{$team->id}}')" data-toggle="tooltip" title="Remove Team" data-trigger="hover"></i></span>
                                    </div>
                                @endif
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

