<div class="modal" id="linking_emp_data" tabindex="-1" role="dialog" aria-labelledby="linkingEmpDataModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="linkingEmpDataModalLabel">Linking Employee Data</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12 linking-emp-data" id='linking_email' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_emp_email">Employee Email :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking Employee Email', 'id'=> 'linking_emp_email']) !!}
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 linking-emp-data" id='linking_phone' style='display:none'>
                        <div class="form-group">
                            <label class="control-label" for="linking_emp_phone_number :*">Employee Phone Number :<span class="text-red">*</span></label>
                            {!! Form::text('', null, ['class' => 'form-control','placeholder' => 'Enter Linking Employee Phone Number', 'id'=> 'linking_emp_phone_number']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::hidden('email', null, ['id' => 'link_emp_email']) !!}
                {!! Form::hidden('type', null, ['id' => 'type']) !!}
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='linkEmpBtn' onClick="linkEmpButton()">Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
