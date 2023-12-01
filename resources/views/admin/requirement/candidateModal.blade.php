<div class="modal fade bd-example-modal-lg" id="candidateModal" tabindex="-1" role="dialog" aria-labelledby="candidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="col-md-3">
                    <h5 class="modal-title" id="candidateModalLabel">Requirement : <span id="jobTitle"></span></h5>
                </div>
                <div class="col-md-8">
                    <div class="float-right">
                        <button class="mr-1 btn btn-primary show-view" onclick="showView()" type="button">View</button>
                        <button class="mr-1 btn btn-outline-warning show-edit" onclick="showEdit()" type="button">Edit</button>
                        <button class="mr-1 btn btn-primary log-button show-logs" onclick="showLogs()" type="button">Show Logs</button>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="close model-close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
            <div class="model-data-view">
                {!! Form::open(['url' => route('candidate.update'), 'id' => 'candidateForm', 'class' => 'form-horizontal','files'=>true]) !!}
                {!! Form::hidden('candidatesubmissionId', null, ['class' => 'form-control', 'id' => 'candidatesubmissionId']) !!}
                <div class="modal-body">
                    <div class="row">
                        @if($hide == 0)
                            <div class="col-md-12 border-bottom mb-2 pb-2" id="requirementData"></div>
                            <div class="col-md-12 border-bottom mb-2 pb-2" id="candidateData"></div>
                        @endif
                        <div class="col-md-12" id="statusUpdate">
                            <div class="row">
                                <div class="col-md-4" @if($isSubmission == 1) style="display: none;" @endif>
                                        <div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
                                            <label class="control-label col-md-12 pl-0" for="status">Status</label>
                                            {!! Form::select('status', \App\Models\Submission::$status, null, ['class' => 'form-control select2','id'=>'candidateStatus', 'style'=>'width:100%']) !!}
                                        </div>
                                </div>
                                <div class="col-md-4 rejection" @if($hide == 0) style="display: none;" @endif>
                                    <div class="form-group{{ $errors->has('common_skills') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-12 pl-0" for="status">Common Skills</label>
                                        {!! Form::select('common_skills', \App\Models\Submission::$percentage, null, ['class' => 'form-control select2','id'=>'common_skills', 'style'=>'width:100%']) !!}
                                    </div>
                                </div>

                                <div class="col-md-4 rejection" @if($hide == 0) style="display: none;" @endif>
                                    <div class="form-group{{ $errors->has('skills_match') ? ' has-error' : '' }}">
                                        <label class="control-label col-md-12 pl-0" for="skills_match">Skills Match</label>
                                        {!! Form::select('skills_match', \App\Models\Submission::$percentage, null, ['class' => 'form-control select2','id'=>'skills_match', 'style'=>'width:100%']) !!}
                                    </div>
                                </div>

                                <div class="col-md-12 rejection" @if($hide == 0) style="display: none;" @endif>
                                    <div class="form-group{{ $errors->has('reason') ? ' has-error' : '' }}">
                                        <label class="control-label" for="reason">Other Reason :<span class="text-red">*</span></label>
                                        {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows'=>4, 'placeholder' => 'Enter Reason', 'id' => 'reason']) !!}
                                    </div>
                                </div>
                                <div class="col-md-6 mt-2" @if($isSubmission != 1) style="display: none;" @endif>
                                    <strong>Status: </strong> <span id='status'></span>
                                </div>
                                <div class="col-md-6 mt-2 rejected-status" @if($isSubmission != 1) style="display: none;" @endif>
                                    <strong>Common Skills: </strong> <span id='common-skill'></span>
                                </div>
                                <div class="col-md-6 mt-2 rejected-status" @if($isSubmission != 1) style="display: none;" @endif>
                                    <strong>Skills Match:</strong> <span id='skill-match'></span>
                                </div>
                                <div class="col-md-6 mt-2 rejected-status" @if($isSubmission != 1) style="display: none;" @endif>
                                    <strong>Other Reason:</strong> <span id='other-reason'></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 border-top mb-2 pb-2 table-responsive" id="historyData"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary model-close" data-dismiss="modal">Close</button>
                    @if($isSubmission == 0)
                        <button type="submit" id="status_submit" class="btn btn-primary">Submit</button>
                    @endif
                </div>
                {!! Form::close() !!}
            </div>
            <div id="model-data-edit" style="display: none" class="model-data-edit">
                <div class="modal-body">
                    <div id="edit-section">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary model-close" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
