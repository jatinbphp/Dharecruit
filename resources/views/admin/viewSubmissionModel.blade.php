<div class="modal fade bd-example-modal-xl" id="viewSubmissionCandidateModal" tabindex="-1" role="dialog" aria-labelledby="viewSubmissionCandidateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewSubmissionCandidateModalLabel">View Submission</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 border-bottom mb-2 pb-2" id="submissionHeadingData"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-2 pb-2" id="submissionHeaderData"></div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-2 pb-2">
                        <div class="col-md-3">
                            <label>
                                {!! Form::checkbox('', 'show-time', null, ['id' => "showTime"]) !!} <span style="margin-right: 10px; color:#AC5BAD; font-weight:bold; ">Show Date/Time</span>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="row">
                        <div class="col-md-12 border-bottom mb-2 pb-2" id="submissionData"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@section('jQuery')
<script type="text/javascript">
    function showPVData(){
        $(".pv-companny-popup-icon").hide();
        $(".pv-company").show();
    }
</script>
@endsection
