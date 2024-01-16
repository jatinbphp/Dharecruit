@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper" style="min-height: 946px;">
        <section class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>{{$menu}}</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-right">
                            <li class="breadcrumb-item"><a href="{{route('setting.index')}}">{{$menu}}</a></li>
                            <li class="breadcrumb-item active">Add</li>
                        </ol>
                    </div>
                </div>
            </div>
        </section>

        <section class="content">
            @include ('admin.error')
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-info">
                        <div class="card-header">
                            <h3 class="card-title">{{$menu}}</h3>
                        </div>
                        {!! Form::open(['url' => route('setting.store'), 'id' => 'settingForm', 'class' => 'form-horizontal','files'=>true]) !!}
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="name">Fill Employer Name :</label>
                                        <div class="col-md-12">
                                            @foreach ($yesNoOptions as $key => $value)
                                                    <?php $checked = (isset($settingData['is_fill_employer_name']) && $settingData['is_fill_employer_name'] == $key) ? 'checked' : '';?>
                                                <label>
                                                    {!! Form::radio('is_fill_employer_name', $key, null, ['class' => 'flat-red',$checked]) !!} <span style="margin-right: 10px">{{ $value }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="no_of_hours_for_expire"># Of Hours Requirement Expired</label>
                                        <div class="col-md-12">
                                            {!! Form::number('no_of_hours_for_expire', (isset($settingData['no_of_hours_for_expire']) && $settingData['no_of_hours_for_expire']) ? $settingData['no_of_hours_for_expire'] : '', ['class' => 'form-control', 'placeholder' => 'Enter # Of Hours Requirement Expired', 'id' => 'no_of_hours_for_expire']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="tooltip_after_no_of_words">Tool Tip After No Of Words</label>
                                        <div class="col-md-12">
                                            {!! Form::number('tooltip_after_no_of_words', (isset($settingData['tooltip_after_no_of_words']) && $settingData['tooltip_after_no_of_words']) ? $settingData['tooltip_after_no_of_words'] : '', ['class' => 'form-control', 'placeholder' => 'Enter Nuber For Show Tool Tip', 'id' => 'tooltip_after_no_of_words']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="heighlight_new_poc_data_days">Heighlight New Poc Data Days</label>
                                        <div class="col-md-12">
                                            {!! Form::number('heighlight_new_poc_data_days', (isset($settingData['heighlight_new_poc_data_days']) && $settingData['heighlight_new_poc_data_days']) ? $settingData['heighlight_new_poc_data_days'] : '', ['class' => 'form-control', 'placeholder' => 'Enter Heighlight New Poc Data Days', 'id' => 'heighlight_new_poc_data_days']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="show_poc_count_days">Show POC Count Days</label>
                                        <div class="col-md-12">
                                            {!! Form::number('show_poc_count_days', (isset($settingData['show_poc_count_days']) && $settingData['show_poc_count_days']) ? $settingData['show_poc_count_days'] : '', ['class' => 'form-control', 'placeholder' => 'Enter Show POC Count Days', 'id' => 'show_poc_count_days']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="show_bet_date_data_on_pv_and_poc_reports">Show bet. Date Data On PV And POC Reports(In Days)</label>
                                        <div class="col-md-12">
                                            {!! Form::number('show_bet_date_data_on_pv_and_poc_reports', (isset($settingData['show_bet_date_data_on_pv_and_poc_reports']) && $settingData['show_bet_date_data_on_pv_and_poc_reports']) ? $settingData['show_bet_date_data_on_pv_and_poc_reports'] : '', ['class' => 'form-control', 'placeholder' => 'Enter Days', 'id' => 'show_bet_date_data_on_pv_and_poc_reports']) !!}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label class="control-label" for="transfer_poc_if_req_not_post_days">Transfer POC If Requirement Not Post Days</label>
                                        <div class="col-md-12">
                                            {!! Form::number('transfer_poc_if_req_not_post_days', (isset($settingData['transfer_poc_if_req_not_post_days']) && $settingData['transfer_poc_if_req_not_post_days']) ? $settingData['transfer_poc_if_req_not_post_days'] : '', ['class' => 'form-control', 'placeholder' => 'Enter Transfer POC If Requirement Not Post Days', 'id' => 'transfer_poc_if_req_not_post_days']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('setting.index') }}" ><button class="btn btn-default" type="button">Cancel</button></a>
                            <button class="btn btn-info float-right" type="submit">Save</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
