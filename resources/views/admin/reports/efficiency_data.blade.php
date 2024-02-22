@if(isset($bdmsData) && $bdmsData && count($bdmsData) && $bdmsData['user_data'] && count($bdmsData['user_data']))
    @if(isset($bdmsData['team_data']) && count($bdmsData['team_data']))
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                @if(isset($bdmsData['team_data']['heading']) && count($bdmsData['team_data']['heading']))
                    <tr>
                        @foreach($bdmsData['team_data']['heading'] as $key => $data)
                            <th>{{$data}}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @if(isset($bdmsData['team_data']['team_wise_data']) && count($bdmsData['team_data']['team_wise_data']))
                    @foreach($bdmsData['team_data']['team_wise_data'] as $key => $rowData)
                        @if($rowData && count($rowData))
                            <tr>
                                @foreach($rowData as $teamId => $data)
                                    <td>{{$data}}</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    @endif
    @php
        $classData = isset($bdmsData['class_data']) ? $bdmsData['class_data'] : [];
        $headings  = isset($bdmsData['heading']) ? $bdmsData['heading'] : [];
        $i=0;
    @endphp
    @if(isset($bdmData['team_data']) && $count($bdmData['team_data']))
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped efficiency-report-table @if($i!=0) mt-3 @endif" id="table-{{$userId}}">
                <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2">BDM: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                </caption>
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border">BDM</th>
                    <th scope="col" colspan="6" class="text-center element-border rm-left-border">POC Data</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Requirement</th>
                    <th scope="col" class="text-center element-border rm-left-border">Submission</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                    <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                    <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                </tr>
                @if($headings && count($headings))
                    <tr>
                        @foreach($headings as $key => $data)
                            @php
                                $bottomRight = (in_array($key, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'heading_tramsfer_out_poc'])) ? 'border-right' : '';
                                $borderLeft = (in_array($key, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                            @endphp
                            <th class="border-bottom {{"$borderLeft $bottomRight"}}">{{$data}}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @if($bdmData && count($bdmData))
                    @foreach($bdmData as $key => $rowData)
                        @if($rowData && count($rowData))
                            <tr>
                                @foreach($rowData as $heading => $data)
                                    @php
                                        $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                        $data = ($data) ? $data : '';
                                        $topBorder = ($key == 'heading') ? 'border-top' : '';
                                        $bottomBorder = (in_array($key,['time_frame', 'heading']) || (isset($bdmData['time_frame']) && !count($bdmData['time_frame']) && $key == 'last_month')) ? 'border-bottom' : '';
                                        $bottomRight = (in_array($heading, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'tramsfer_out_poc'])) ? 'border-right' : '';
                                        $borderLeft = (in_array($heading, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                    @endphp
                                    <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    @endif
    <div class="col-md-12 mt-3 p-3 border border-with-label user-wise-data" data-label="">
        @foreach($bdmsData['user_data'] as $userId => $bdmData)
            <div class="table-responsive m-lg-n2">
                <table class="table table-bordered table-striped efficiency-report-table @if($i!=0) mt-3 @endif" id="table-{{$userId}}">
                    <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2">BDM: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center element-border">BDM</th>
                            <th scope="col" colspan="6" class="text-center element-border rm-left-border">POC Data</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Requirement</th>
                            <th scope="col" class="text-center element-border rm-left-border">Submission</th>
                            <th scope="col" colspan="4" class="text-center element-border rm-left-border">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                            <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                            <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                        </tr>
                        @if($headings && count($headings))
                            <tr>
                                @foreach($headings as $key => $data)
                                    @php
                                       $bottomRight = (in_array($key, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'heading_tramsfer_out_poc'])) ? 'border-right' : '';
                                       $borderLeft = (in_array($key, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                    @endphp
                                        <th class="border-bottom {{"$borderLeft $bottomRight"}}">{{$data}}</th>
                                @endforeach
                            </tr>
                        @endif
                        </thead>
                    <tbody>
                    @if($bdmData && count($bdmData))
                        @foreach($bdmData as $key => $rowData)
                                @if($rowData && count($rowData))
                                    <tr>
                                        @foreach($rowData as $heading => $data)
                                            @php
                                                $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                                $data = ($data) ? $data : '';
                                                $topBorder = ($key == 'heading') ? 'border-top' : '';
                                                $bottomBorder = (in_array($key,['time_frame', 'heading']) || (isset($bdmData['time_frame']) && !count($bdmData['time_frame']) && $key == 'last_month')) ? 'border-bottom' : '';
                                                $bottomRight = (in_array($heading, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'tramsfer_out_poc'])) ? 'border-right' : '';
                                                $borderLeft = (in_array($heading, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                            @endphp
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                        @endforeach
                                    </tr>
                                @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            @php $i++; @endphp
        @endforeach
    </div>
@endif
@if(isset($recruitersData) && $recruitersData && count($recruitersData) && isset($recruitersData['user_data']) && count($recruitersData['user_data']))
    @if(isset($recruitersData['team_data']) && count($recruitersData['team_data']))
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                @if(isset($recruitersData['team_data']['heading']) && count($recruitersData['team_data']['heading']))
                    <tr>
                        @foreach($recruitersData['team_data']['heading'] as $key => $data)
                            <th>{{$data}}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @if(isset($recruitersData['team_data']['team_wise_data']) && count($recruitersData['team_data']['team_wise_data']))
                    @foreach($recruitersData['team_data']['team_wise_data'] as $key => $rowData)
                        @if($rowData && count($rowData))
                            <tr>
                                @foreach($rowData as $teamId => $data)
                                    <td>{{$data}}</td>
                                @endforeach
                            </tr>
                        @endif
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    @endif
    @php
        $classData = isset($recruitersData['class_data']) ? $recruitersData['class_data'] :  [];
        $headings  = isset($recruitersData['heading']) ? $recruitersData['heading'] : [];
        $i=0;
    @endphp
    <div class="col-md-12 mt-3 p-3 border border-with-label user-wise-data" data-label="">
        @foreach($recruitersData['user_data'] as $userId => $recruitersData)
            <div class="table-responsive m-lg-n2">
                <table class="table table-bordered table-striped efficiency-report-table @if($i!=0) mt-3 @endif" id="table-{{$userId}}">
                    <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2">Recruiter: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                    <tr>
                        <th scope="col" class="text-center element-border">Recruiter</th>
                        <th scope="col" colspan="4" class="text-center element-border rm-left-border">Employee Data</th>
                        <th scope="col" colspan="4" class="text-center element-border rm-left-border">Requirement</th>
                        <th scope="col" colspan="2" class="text-center element-border rm-left-border">Submission</th>
                        <th scope="col" colspan="4" class="text-center element-border rm-left-border">BDM Status</th>
                        <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                        <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                        <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                    </tr>
                    @if($headings && count($headings))
                        <tr>
                            @foreach($headings as $key => $data)
                                @php
                                    $bottomRight = (in_array($key, ['heading_recruiter', 'heading_type','heading_servable_per', 'heading_uniq_sub', 'heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_interview_count', 'heading_client_backout', 'heading_backout', 'heading_new_employee'])) ? 'border-right' : '';
                                    $borderLeft = (in_array($key, ['heading_type', 'heading_recruiter'])) ? 'border-left' : '';
                                @endphp
                                <th class="border-bottom {{"$borderLeft $bottomRight"}}">{{$data}}</th>
                            @endforeach
                        </tr>
                    @endif
                    </thead>
                    <tbody>
                    @if($recruitersData && count($recruitersData))
                        @foreach($recruitersData as $key => $rowData)
                            @if($rowData && count($rowData))
                                <tr>
                                    @foreach($rowData as $heading => $data)
                                        @php
                                            $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                            $data = ($data) ? $data : '';
                                            $topBorder = ($key == 'heading') ? 'border-top' : '';
                                            $bottomBorder = (in_array($key,['time_frame', 'heading']) || (isset($recruitersData['time_frame']) && !count($recruitersData['time_frame']) && $key == 'last_month')) ? 'border-bottom' : '';
                                            $bottomRight = (in_array($heading, ['heading_recruiter', 'heading_type','heading_servable_per', 'servable_per', 'heading_uniq_sub', 'unique_submission_sent','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'interview_count', 'client_backout', 'new_employee'])) ? 'border-right' : '';
                                            $borderLeft = (in_array($heading, ['heading_type', 'heading_recruiter'])) ? 'border-left' : '';
                                        @endphp
                                        <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                    @endforeach
                                </tr>
                            @endif
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
            @php $i++; @endphp
        @endforeach
    </div>
@endif
@if(isset($bdmTimeFrame) && $bdmTimeFrame && count($bdmTimeFrame))
    @if(isset($bdmTimeFrame['user_data']) && count($bdmTimeFrame['user_data']))
        @php
            $classData = isset($bdmTimeFrame['class_data']) ? $bdmTimeFrame['class_data'] : [];
            $headings  = isset($bdmTimeFrame['heading']) ? $bdmTimeFrame['heading'] : [];
        @endphp
            <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
                <div class="table-responsive m-lg-n2">
                    <table class="table table-bordered table-striped efficiency-report-table" id="bdm_time_frame">
                        <caption class="text-bold py-0">
                            <span class="badge badge-info my-2 p-2">BDM: Time Frame</span>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" class="text-center element-border">BDM</th>
                            <th scope="col" colspan="6" class="text-center element-border rm-left-border">POC Data</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Requirement</th>
                            <th scope="col" class="text-center element-border rm-left-border">Submission</th>
                            <th scope="col" colspan="4" class="text-center element-border rm-left-border">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                            <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                            <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                        </tr>
                        @if($headings && count($headings))
                            <tr>
                                @foreach($headings as $key => $data)
                                    @php
                                        $bottomRight = (in_array($key, ['heading_time_frame', 'heading_type', 'heading_servable_per', 'heading_sub_rec', 'heading_interview_count', 'heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_client_backout', 'heading_backout', 'heading_tramsfer_out_poc'])) ? 'border-right' : '';
                                        $borderLeft = (in_array($key, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                    @endphp
                                    <th class="border-bottom {{"$borderLeft $bottomRight"}}">{{$data}}</th>
                                @endforeach
                            </tr>
                        @endif
                        </thead>
                        <tbody>
                        @php
                            $totalCount = count($bdmTimeFrame['user_data']);
                            $i = 1;
                        @endphp
                            @foreach($bdmTimeFrame['user_data'] as $key => $bdmData)
                                @if($bdmData && count($bdmData))
                                    <tr>
                                        @foreach($bdmData as $heading => $data)
                                            @php
                                                $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                                $data = ($data) ? $data : '';
                                                $topBorder = ($key == 'heading') ? 'border-top' : '';
                                                $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                                $bottomRight = (in_array($heading, ['heading_time_frame', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'interview_count', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout','tramsfer_out_poc'])) ? 'border-right' : '';
                                                $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                            @endphp
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                        @endforeach
                                    </tr>
                                @endif
                                @php $i++; @endphp
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    @endif
@endif
@if(isset($recruiterTimeFrame) && $recruiterTimeFrame && count($recruiterTimeFrame) && isset($recruiterTimeFrame['user_data']) && count($recruiterTimeFrame['user_data']))
    @php
        $classData = isset($recruiterTimeFrame['class_data']) ? $recruiterTimeFrame['class_data'] : [];
        $headings  = isset($recruiterTimeFrame['heading']) ? $recruiterTimeFrame['heading'] : [];
    @endphp
    <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped efficiency-report-table" id="rec_time_frame">
                <caption class="text-bold py-0">
                    <span class="badge badge-info my-2 p-2">Recruiter: Time Frame</span>
                </caption>
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border">Recruiter</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Employee Data</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Requirement</th>
                    <th scope="col" colspan="2" class="text-center element-border rm-left-border">Submission</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                    <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                    <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                </tr>
                @if($headings && count($headings))
                    <tr>
                        @foreach($headings as $key => $data)
                            @php
                                $bottomRight = (in_array($key, ['heading_recruiter', 'heading_new_employee', 'heading_time_frame' ,'heading_type','heading_servable_per', 'heading_uniq_sub','heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_client_backout', 'heading_backout'])) ? 'border-right' : '';
                                $borderLeft = (in_array($key, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                            @endphp
                            <th class="border-bottom {{"$borderLeft $bottomRight"}}">{{$data}}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @php
                    $totalCount = count($recruiterTimeFrame['user_data']);
                    $i = 1;
                @endphp
                @foreach($recruiterTimeFrame['user_data'] as $key => $recruiterData)
                    @if($recruiterData && count($recruiterData))
                        <tr>
                            @foreach($recruiterData as $heading => $data)
                                @php
                                    $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                    $data = ($data) ? $data : '';
                                    $topBorder = ($key == 'heading') ? 'border-top' : '';
                                    $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                    $bottomRight = (in_array($heading, ['heading_recruiter', 'new_employee', 'heading_time_frame' ,'heading_type','heading_servable_per', 'servable_per', 'heading_uniq_sub', 'unique_submission_sent','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout'])) ? 'border-right' : '';
                                    $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                @endphp
                                <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                            @endforeach
                        </tr>
                    @endif
                    @php $i++ @endphp
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
