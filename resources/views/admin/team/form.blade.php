{!! Form::hidden('redirects_to', URL::previous()) !!}
<div class="row">
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('team_type') ? ' has-error' : '' }}">
            <label class="control-label" for="team_type">Select Team Type:<span class="text-red">*</span></label>
            {!! Form::select('team_type', \App\Models\Team::getTeamType(), null, ['class' => 'form-control select2','id'=>'team_type']) !!}
            <span class="text-danger">
                <strong class='error' data-input="team_type"></strong>
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('team_name') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="team_name">Team Name :<span class="text-red">*</span></label>
            {!! Form::text('team_name', null, ['class' => 'form-control', 'placeholder' => 'Enter Team Name', 'id' => 'team_name']) !!}
            <span class="text-danger">
                <strong class='error' id="team_name_error" data-input="team_name"></strong>
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('team_lead') ? ' has-error' : '' }}">
            <label class="control-label" for="team_lead">Select Team Lead:<span class="text-red">*</span></label>
            {!! Form::select('team_lead', [], null, ['class' => 'form-control select2','id'=>'team_lead']) !!}
            <span class="text-danger">
                <strong class='error' data-input="team_lead"></strong>
            </span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group{{ $errors->has('team_color') ? ' has-error' : '' }}">
            <label class="col-md-12 control-label" for="team_color">Team Color :<span class="text-red">*</span></label>
            <div class="input-group my-colorpicker2 colorpicker-element" data-colorpicker-id="2">
                {!! Form::text('team_color', null, ['class' => 'form-control', 'placeholder' => 'Enter Team Color', 'id' => 'team_color', 'data-original-title' => '', 'title' => '']) !!}
                <div class="input-group-append">
                    <span class="input-group-text"><i class="fas fa-square"></i></span>
                </div>
            </div>
            <span class="text-danger">
                <strong class='error' data-input="team_color"></strong>
            </span>
        </div>
    </div>
    <div class="col-md-12">
        <div class="form-group{{ $errors->has('team_member') ? ' has-error' : '' }}" id='team_members_section' style="display: none">
            <label class="col-md-12 control-label" for="team_color">Team Member :<span class="text-red">*</span></label>
            <div class="border border-dark rounded p-2" id="team_member">
            </div>
            <span class="text-danger">
                <strong class='error' data-input="team_members[]"></strong>
            </span>
        </div>
    </div>
</div>
