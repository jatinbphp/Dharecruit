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
    <div class="col-md-12 mt-3 p-3 border border-with-label user-wise-data" data-label="">
        @foreach($bdmsData['user_data'] as $userId => $bdmData)
            <div class="table-responsive m-lg-n2">
                <table class="table table-bordered table-striped efficiency-report-table @if($i!=0) mt-3 @endif" id="table-{{$userId}}">
                    <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2 sticky-badge">BDM: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center element-border sticky-col">BDM</th>
                            <th scope="col" colspan="6" class="text-center element-border rm-left-border">POC Data</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Requirement</th>
                            <th scope="col" colspan="2" class="text-center element-border rm-left-border">Submission</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                            <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                            <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                        </tr>
                        @if($headings && count($headings))
                            <tr>
                                @foreach($headings as $key => $data)
                                    @php
                                       $bottomRight = (in_array($key, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_avg_time', 'heading_submission_received', 'avg_time', 'heading_un_viewed', 'bdm_unviewed', 'heading_interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'heading_tramsfer_out_poc'])) ? 'border-right' : '';
                                       $borderLeft = (in_array($key, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                       $scrollClass = '';
                                       if($key == 'heading_bdm'){
                                           $scrollClass = 'sticky-col';
                                       }
                                    @endphp
                                        <th class="border-bottom {{"$scrollClass $borderLeft $bottomRight"}}">{{$data}}</th>
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
                                                $bottomRight = (in_array($heading, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_avg_time', 'heading_submission_received', 'avg_time', 'heading_un_viewed', 'bdm_unviewed', 'interview_count', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'tramsfer_out_poc'])) ? 'border-right' : '';
                                                $borderLeft = (in_array($heading, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                                $scrollClass = '';
                                                if($heading == 'heading_type'){
                                                    $scrollClass = 'sticky-col';
                                                }
                                            @endphp
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight $scrollClass"}}">{{$data}}</td>
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
                            class="badge badge-info my-2 p-2 sticky-badge">Recruiter: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                    <tr>
                        <th scope="col" class="text-center element-border sticky-col">Recruiter</th>
                        <th scope="col" colspan="4" class="text-center element-border rm-left-border">Employee Data</th>
                        <th scope="col" colspan="4" class="text-center element-border rm-left-border">Requirement</th>
                        <th scope="col" colspan="3" class="text-center element-border rm-left-border">Submission</th>
                        <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                        <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                        <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                        <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                    </tr>
                    @if($headings && count($headings))
                        <tr>
                            @foreach($headings as $key => $data)
                                @php
                                    $bottomRight = (in_array($key, ['heading_recruiter', 'heading_type','heading_servable_per', 'heading_avg_time', 'heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_interview_count', 'heading_client_backout', 'heading_backout', 'heading_new_employee'])) ? 'border-right' : '';
                                    $borderLeft = (in_array($key, ['heading_type', 'heading_recruiter'])) ? 'border-left' : '';
                                    $scrollClass = '';
                                    if($key == 'heading_recruiter'){
                                       $scrollClass = 'sticky-col';
                                   }
                                @endphp
                                <th class="border-bottom {{"$borderLeft $bottomRight $scrollClass"}}">{{$data}}</th>
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
                                            $bottomRight = (in_array($heading, ['heading_recruiter', 'heading_type','heading_servable_per', 'servable_per', 'heading_avg_time', 'avg_time','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'interview_count', 'client_backout', 'new_employee'])) ? 'border-right' : '';
                                            $borderLeft = (in_array($heading, ['heading_type', 'heading_recruiter'])) ? 'border-left' : '';
                                            $scrollClass = '';
                                            if($heading == 'heading_type'){
                                                $scrollClass = 'sticky-col';
                                            }
                                        @endphp
                                        <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight $scrollClass"}}">{{$data}}</td>
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
                            <span class="badge badge-info my-2 p-2 sticky-badge">BDM: Time Frame</span>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" class="text-center element-border sticky-col">BDM</th>
                            <th scope="col" colspan="6" class="text-center element-border rm-left-border">POC Data</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Requirement</th>
                            <th scope="col" colspan="2" class="text-center element-border rm-left-border">Submission</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                            <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                            <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                        </tr>
                        @if($headings && count($headings))
                            <tr>
                                @foreach($headings as $key => $data)
                                    @php
                                        $bottomRight = (in_array($key, ['heading_time_frame', 'heading_type', 'heading_servable_per', 'heading_avg_time', 'heading_interview_count', 'heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_client_backout', 'heading_backout', 'heading_tramsfer_out_poc'])) ? 'border-right' : '';
                                        $borderLeft = (in_array($key, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                        $scrollClass = '';
                                        if($key == 'heading_time_frame'){
                                           $scrollClass = 'sticky-col';
                                       }
                                    @endphp
                                    <th class="border-bottom {{"$borderLeft $bottomRight $scrollClass"}}">{{$data}}</th>
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
                                                $bottomRight = (in_array($heading, ['heading_time_frame', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_avg_time', 'interview_count', 'heading_submission_received', 'avg_time', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout','tramsfer_out_poc'])) ? 'border-right' : '';
                                                $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                                $scrollClass = '';
                                                if($heading == 'heading_type'){
                                                    $scrollClass = 'sticky-col';
                                                }
                                            @endphp
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight $scrollClass"}}">{{$data}}</td>
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
                    <span class="badge badge-info my-2 p-2 sticky-badge">Recruiter: Time Frame</span>
                </caption>
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border sticky-col">Recruiter</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Employee Data</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Requirement</th>
                    <th scope="col" colspan="3" class="text-center element-border rm-left-border">Submission</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                    <th scope="col" colspan="1" class="text-center element-border rm-left-border">Interview</th>
                    <th scope="col" colspan="7" class="text-center element-border rm-left-border">Client Status</th>
                </tr>
                @if($headings && count($headings))
                    <tr>
                        @foreach($headings as $key => $data)
                            @php
                                $bottomRight = (in_array($key, ['heading_recruiter', 'heading_new_employee', 'heading_time_frame' ,'heading_type','heading_servable_per', 'heading_avg_time','heading_submission_received', 'heading_un_viewed', 'heading_position_closed', 'heading_client_backout', 'heading_backout','heading_interview_count'])) ? 'border-right' : '';
                                $borderLeft = (in_array($key, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                $scrollClass = '';
                                if($key == 'heading_time_frame'){
                                   $scrollClass = 'sticky-col';
                               }
                            @endphp
                            <th class="border-bottom {{"$borderLeft $bottomRight $scrollClass"}}">{{$data}}</th>
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
                                    $bottomRight = (in_array($heading, ['heading_recruiter', 'new_employee', 'heading_time_frame' ,'heading_type','heading_servable_per', 'servable_per', 'heading_avg_time', 'avg_time','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout', 'interview_count'])) ? 'border-right' : '';
                                    $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                    $scrollClass = '';
                                    if($heading == 'heading_type'){
                                        $scrollClass = 'sticky-col';
                                    }
                                @endphp
                                <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight $scrollClass"}}">{{$data}}</td>
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
