<div class="modal fade" id="transferPoc" tabindex="-1" role="dialog" aria-labelledby="transferPocLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferPocLabel">Transfer POC</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <label class="control-label">PV Company Name:</label>
                        <span id="pv_company_name"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">POC Name:</label>
                        <span id="poc_name"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">POC Email:</label>
                        <span id="poc_email"></span>
                    </div>
                    <div class="col-md-6">
                        <label class="control-label">POC Phone:</label>
                        <span id="poc_phone"></span>
                    </div>
                </div>
                <div class="row" style="border-top: 1px solid #e9ecef">
                    <div class="col-md-12 mt-3">
                        <label class="control-label" for="bdm">Transfer BDM:</label>
                    </div>
                    <div class="col-md-12">
                        @php
                            $bdms = \App\Models\Admin::getActiveBDM(true);
                            unset($bdms[getLoggedInUserId()]);
                        @endphp
                        {!! Form::select('bdm', $bdms, null, ['class' => 'form-control select2','id'=>'bdm','style'=>'width:100%']) !!}
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
