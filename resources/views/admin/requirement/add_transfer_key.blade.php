<div class="modal fade" id="addTransferKey" tabindex="-1" role="dialog" aria-labelledby="addTransferKeyLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTransferKeyLabel">Add Transfer Key</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <label class="control-label" for="pv_company_name">Transfer Key :<span class="text-red">*</span></label>
                        {!! Form::text('transfer_key', null, ['class' => 'form-control','placeholder' => 'Enter Transfer Key', 'id'=> 'transfer_key']) !!}
                        {!! Form::hidden('', null, ['class' => 'form-control', 'id'=> 'model_poc_email']) !!}
                        <span class="text-danger"><strong id="transfer_key_error"></strong></span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id='transferKey'>Save</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
