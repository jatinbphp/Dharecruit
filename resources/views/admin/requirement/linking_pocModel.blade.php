<div class="modal" id="linking_poc_data" tabindex="-1" role="dialog" aria-labelledby="linkingPocDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkingPocDataModalLabel">Linking POC Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 linking-pov-data" id='linking_email' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_poc_email">POC Email :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking POC Email', 'id'=> 'linking_poc_email']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 linking-pov-data" id='linking_poc_phone' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_poc_phone_number :*">POC Phone Number :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking POC Phone Number', 'id'=> 'linking_poc_phone_number']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 linking-pov-data" id='linking_location' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_poc_location :*">POC Location :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking POC Location', 'id'=> 'linking_poc_location']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 linking-pov-data" id='linking_pv_location' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_pv_company_location : :*">PV Company Location :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking PV Company Location', 'id'=> 'linking_pv_company_location']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::hidden('email', null, ['id' => 'email']) !!}
                {!! Form::hidden('type', null, ['id' => 'type']) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='linkBtn'>Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
