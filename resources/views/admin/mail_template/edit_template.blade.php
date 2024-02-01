{!! Form::model($templateData,['id' => "mailTemplateForm_$templateData->id ",'class' => 'form-horizontal mail-template','files'=>true]) !!}
<div class="card-body p-0">
    <div class="row mt-3 pl-3 pr-3">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label" for="subject_$templateData->id">Mail Subject :<span class="text-red">*</span></label>
                {!! Form::text('subject', null, ['class' => 'form-control', 'placeholder' => 'Enter Mail Subject', 'id' => "subject_$templateData->id"]) !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label" for="content_$templateData->id">Mail Content :<span class="text-red">*</span></label>
                {!! Form::textarea('content', null, ['class' => 'form-control description', 'rows'=>4, 'placeholder' => 'Enter Mail Content', 'id' => "content_$templateData->id"]) !!}
            </div>
        </div>
    </div>
</div>
<div class="card-footer">
    <button class="btn btn-info float-right" onclick="updateMailTemplate('{{$templateData->id}}')" type="button">Update</button>
</div>
{!! Form::close() !!}

