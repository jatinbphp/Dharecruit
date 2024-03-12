@if(isset($employerFilterData) && $employerFilterData && count($employerFilterData) && isset($employerFilterData['employer_company_data']) && count($employerFilterData['employer_company_data']))
    @php
        $classData = isset($employerFilterData['class_data']) ? $employerFilterData['class_data'] : [];
        $headings  = isset($employerFilterData['heading']) ? $employerFilterData['heading'] : [];
        $emptyEmployerRows = isset($employerFilterData['empty_employer_rows']) ? $employerFilterData['empty_employer_rows'] : [];
        $emptyPOCRows = isset($employerFilterData['empty_poc_rows']) ? $employerFilterData['empty_poc_rows'] : [];
    @endphp
    <div class="col-md-12 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped" id="employer_company_report">
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border" colspan="2">Company Name</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Submission</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                    <th scope="col" colspan="8" class="text-center element-border rm-left-border">Client Status</th>
                    <th scope="col" colspan="3" class="text-center element-border rm-left-border">Employee</th>
                    <th scope="col" colspan="2" class="text-center element-border rm-left-border">Recruiter / Category</th>
                </tr>
                @if($headings && count($headings))
                    <tr>
                        @foreach($headings as $key => $data)
                            @php
                                $bottomRight = (in_array($key, ['employee_count', 'unique_sub_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'rec_count', 'rec_wise_count'])) ? 'border-right' : '';
                                $borderLeft = ($key == 'company_name') ? 'border-left' : '';
                            @endphp
                            <th onclick="sortData(this)" class="border-bottom {{"$borderLeft $bottomRight $key"}}">
                                <div class="d-inline">{{$data}}</div>
                                <div class="d-inline"><span class="sort-indicator asc p-0">&uarr;</span><span class="sort-indicator desc p-0">&darr;</span></div>
                            </th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                @php
                    $totalCount = count($employerFilterData['employer_company_data']);
                    $i = 1;
                @endphp
                @foreach($employerFilterData['employer_company_data'] as $key => $employerCompanyData)
                    @php
                        $key = strtolower(str_replace([' ', '.'], ['_', ''], $key));
                    @endphp
                    <tr class="employer-company-{{$key}} @if(in_array($key, $emptyEmployerRows)) empty-row @endif parent">
                        @if($employerCompanyData && count($employerCompanyData))
                            @foreach($employerCompanyData as $heading => $data)
                                @php
                                    $data = ($data) ? $data : '';
                                    $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                    $topBorder = ($key == 'heading') ? 'border-top' : '';
                                    $bottomBorder = (in_array($key, ['heading'])) ? 'border-bottom' : '';
                                    $bottomRight = (in_array($heading, ['employee_count', 'unique_sub_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'rec_count', 'rec_wise_count'])) ? 'border-right' : '';
                                    $borderLeft = ($heading == 'company_name') ? 'border-left' : '';
                                @endphp
                                @if(is_array($data))
                                    <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight $heading"}} border-bottom employer-company-group-{{$key}}">
                                    @foreach($data as $rowData)
                                        <div class="{{$class}}">{{$rowData}}</div>
                                    @endforeach
                                    </td>
                                @else
                                    <td class="{{"$topBorder $borderLeft $bottomRight $heading"}} border-bottom employer-company-group-{{$key}}">
                                        <div class="data">
                                            <span class="{{$class}}">
                                                {{$data}}
                                            </span>
                                        </div>
                                        @if(strtolower($heading) == 'company_name')
                                            <div class="expand-toggle mt-3">
                                                <button class="btn btn-sm btn-default hide-rows" title="Show Employee of {{$data}}" data-company-id="{{$key}}" onclick="toggleButton(this, 'employer-company')" data-toggle="collapse" data-target=".collapse-{{$key}}">
                                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                @endif
                            @endforeach
                        @endif
                    </tr>
                    @if(isset($employerFilterData['employee_data'][$key]) && $employerFilterData['employee_data'][$key])
                        @foreach($employerFilterData['employee_data'][$key] as $employeeName => $employeeData)
                            <tr class="collapse collapse-{{$key}} @if(in_array($key.'_'.$employeeName, $emptyPOCRows)) empty-row @endif child">
                                @if($employeeData && count($employeeData))
                                    @foreach($employeeData as $heading => $data)
                                        @php
                                            $data = ($data) ? $data : '';
                                            $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                            $topBorder = ($key == 'heading') ? 'border-top' : '';
                                            $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                            $bottomRight = (in_array($heading, ['employee_count', 'unique_sub_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'rec_count', 'rec_wise_count'])) ? 'border-right' : '';
                                            $borderLeft = ($heading == 'company_name') ? 'border-left' : '';
                                        @endphp
                                        @if(strtolower($key) == 'heading')
                                            <th class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}}"><span>{{$data}}</span></th>
                                        @else
                                            @if(is_array($data))
                                                <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight $heading"}} employer-company-group-{{$key}}">
                                                    @foreach($data as $rowData)
                                                        <div class="{{$class}}">{{$rowData}}</div>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight $heading"}} employer-company-group-{{$key}}"><span class="{{$class}}">{{$data}}</span></td>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    @else
                        @if(strtolower($key) != 'heading')
                            <tr class="collapse collapse-{{$key}}">
                                <td class="element-border text-danger text-bold employer-company-group-{{$key}}" colspan="{{count($employerCompanyData)}}">No Employee Data Found.</td>
                            </tr>
                        @endif
                    @endif
                    @php $i++ @endphp
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif


