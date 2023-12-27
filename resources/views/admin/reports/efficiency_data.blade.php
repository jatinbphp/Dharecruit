@if(isset($bdmsData) && $bdmsData && count($bdmsData) && $bdmsData['user_data'] && count($bdmsData['user_data']))
    @php
        $classData = isset($bdmsData['class_data']) ? $bdmsData['class_data'] : [];
        $i=0;
    @endphp
    <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
        @foreach($bdmsData['user_data'] as $userId => $bdmData)
            <div class="table-responsive m-lg-n2">
                <table class="table table-bordered table-striped @if($i!=0) mt-3 @endif">
                    <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2">BDM: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                    <tr>
                        <th scope="col" class="text-center element-border">BDM</th>
                        <th scope="col" colspan="5" class="text-center element-border">Requirement</th>
                        <th scope="col" class="text-center element-border">Submission</th>
                        <th scope="col" colspan="4" class="text-center element-border">BDM Status</th>
                        <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                        <th scope="col" colspan="6" class="text-center element-border">Client Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($bdmData && count($bdmData))
                        @foreach($bdmData as $key => $rowData)
                            <tr>
                                @if($rowData && count($rowData))
                                    @foreach($rowData as $heading => $data)
                                        @php
                                            $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                            $data = ($data) ? $data : '-';
                                            $topBorder = ($key == 'heading') ? 'border-top' : '';
                                            $bottomBorder = (in_array($key,['time_frame', 'heading']) || (isset($bdmData['time_frame']) && !count($bdmData['time_frame']) && $key == 'last_month')) ? 'border-bottom' : '';
                                            $bottomRight = (in_array($heading, ['heading_bdm', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout'])) ? 'border-right' : '';
                                            $borderLeft = (in_array($heading, ['heading_type', 'heading_bdm'])) ? 'border-left' : '';
                                        @endphp
                                        @if(strtolower($key) == 'heading')
                                            <th class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</th>
                                        @else
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
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
    @php
        $classData = isset($recruitersData['class_data']) ? $recruitersData['class_data'] :  [];
        $i=0;
    @endphp
    <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
        @foreach($recruitersData['user_data'] as $userId => $recruitersData)
            <div class="table-responsive m-lg-n2">
                <table class="table table-bordered table-striped @if($i!=0) mt-3 @endif">
                    <caption class="text-bold py-0">
                        <span
                            class="badge badge-info my-2 p-2">Recruiter: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                    </caption>
                    <thead>
                    <tr>
                        <th scope="col" class="text-center element-border">Recruiter</th>
                        <th scope="col" colspan="4" class="text-center element-border">Requirement</th>
                        <th scope="col" colspan="2" class="text-center element-border">Submission</th>
                        <th scope="col" colspan="4" class="text-center element-border">BDM Status</th>
                        <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                        <th scope="col" colspan="6" class="text-center element-border">Client Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($recruitersData && count($recruitersData))
                        @foreach($recruitersData as $key => $rowData)
                            <tr>
                                @if($rowData && count($rowData))
                                    @foreach($rowData as $heading => $data)
                                        @php
                                            $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                            $data = ($data) ? $data : '-';
                                            $topBorder = ($key == 'heading') ? 'border-top' : '';
                                            $bottomBorder = (in_array($key,['time_frame', 'heading']) || (isset($recruitersData['time_frame']) && !count($recruitersData['time_frame']) && $key == 'last_month')) ? 'border-bottom' : '';
                                            $bottomRight = (in_array($heading, ['heading_recruiter', 'heading_type','heading_servable_per', 'servable_per', 'heading_uniq_sub', 'unique_submission_sent','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout'])) ? 'border-right' : '';
                                            $borderLeft = (in_array($heading, ['heading_type', 'heading_recruiter'])) ? 'border-left' : '';
                                            @endphp
                                        @if(strtolower($key) == 'heading')
                                            <th class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</th>
                                        @else
                                            <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
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
        @endphp
            <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
                <div class="table-responsive m-lg-n2">
                    <table class="table table-bordered table-striped">
                        <caption class="text-bold py-0">
                            <span class="badge badge-info my-2 p-2">BDM: Time Frame</span>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" class="text-center element-border">BDM</th>
                            <th scope="col" colspan="5" class="text-center element-border">Requirement</th>
                            <th scope="col" class="text-center element-border">Submission</th>
                            <th scope="col" colspan="4" class="text-center element-border">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                            <th scope="col" colspan="6" class="text-center element-border">Client Status</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php
                            $totalCount = count($bdmTimeFrame['user_data']);
                            $i = 1;
                        @endphp
                            @foreach($bdmTimeFrame['user_data'] as $key => $bdmData)
                                <tr>
                                    @if($bdmData && count($bdmData))
                                        @foreach($bdmData as $heading => $data)
                                            @php
                                                $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                                $data = ($data) ? $data : '-';
                                                $topBorder = ($key == 'heading') ? 'border-top' : '';
                                                $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                                $bottomRight = (in_array($heading, ['heading_time_frame', 'heading_type', 'heading_servable_per', 'servable_per', 'heading_sub_rec', 'heading_submission_received', 'submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout'])) ? 'border-right' : '';
                                                $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                                @endphp
                                            @if(strtolower($key) == 'heading')
                                                <th class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</th>
                                            @else
                                                <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
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
    @endphp
    <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped">
                <caption class="text-bold py-0">
                    <span class="badge badge-info my-2 p-2">Recruiter: Time Frame</span>
                </caption>
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border">Recruiter</th>
                    <th scope="col" colspan="4" class="text-center element-border">Requirement</th>
                    <th scope="col" colspan="2" class="text-center element-border">Submission</th>
                    <th scope="col" colspan="4" class="text-center element-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                    <th scope="col" colspan="6" class="text-center element-border">Client Status</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $totalCount = count($recruiterTimeFrame['user_data']);
                    $i = 1;
                @endphp
                @foreach($recruiterTimeFrame['user_data'] as $key => $recruiterData)
                    <tr>
                        @if($recruiterData && count($recruiterData))
                            @foreach($recruiterData as $heading => $data)
                                @php
                                    $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                    $data = ($data) ? $data : '-';
                                    $topBorder = ($key == 'heading') ? 'border-top' : '';
                                    $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                    $bottomRight = (in_array($heading, ['heading_recruiter', 'heading_type','heading_servable_per', 'servable_per', 'heading_uniq_sub', 'unique_submission_sent','heading_submission_received', 'heading_un_viewed', 'bdm_unviewed', 'heading_position_closed', 'vendor_position_closed', 'heading_client_backout', 'heading_backout', 'client_backout'])) ? 'border-right' : '';
                                    $borderLeft = (in_array($heading, ['heading_type', 'heading_time_frame'])) ? 'border-left' : '';
                                @endphp
                                @if(strtolower($key) == 'heading')
                                    <th class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</th>
                                @else
                                    <td class="{{"$class $topBorder $bottomBorder $borderLeft $bottomRight"}}">{{$data}}</td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                    @php $i++ @endphp
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
