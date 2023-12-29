@if(isset($pvFilterData) && $pvFilterData && count($pvFilterData) && isset($pvFilterData['pv_company_data']) && count($pvFilterData['pv_company_data']))
    @php
        $classData = isset($pvFilterData['class_data']) ? $pvFilterData['class_data'] : [];
    @endphp
    <div class="col-md-12 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border">Company Name</th>
                    <th scope="col" colspan="4" class="text-center element-border">Requirement</th>
                    <th scope="col" colspan="5" class="text-center element-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                    <th scope="col" colspan="8" class="text-center element-border">Client Status</th>
                    <th scope="col" colspan="4" class="text-center element-border">POC</th>
                    <th scope="col" colspan="2" class="text-center element-border">BDM / Category</th>
                </tr>
                </thead>
                <tbody>
                @php
                    $totalCount = count($pvFilterData['pv_company_data']);
                    $i = 1;
                @endphp
                @foreach($pvFilterData['pv_company_data'] as $key => $pvCompanyData)
                    @php
                        $key = strtolower(str_replace([' ', '.'], ['_', ''], $key));
                    @endphp
                    <tr class="pv-company-{{$key}}">
                        @if($pvCompanyData && count($pvCompanyData))
                            @foreach($pvCompanyData as $heading => $data)
                                @php
                                    $class = (isset($classData[$heading])) ? $classData[$heading] : '';
                                    //$data = ($data) ? $data : '-';
                                    $topBorder = ($key == 'heading') ? 'border-top' : '';
                                    $bottomBorder = (in_array($key, ['heading'])) ? 'border-bottom' : '';
                                    $bottomRight = (in_array($heading, ['company_name', 'unique_req_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'bdm_count', 'bdm_wise_count'])) ? 'border-right' : '';
                                    $borderLeft = ($heading == 'company_name') ? 'border-left' : '';
                                @endphp
                                @if(strtolower($key) == 'heading')
                                    <th class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}}"><span>{{$data}}</span></th>
                                @else
                                    @if(is_array($data))
                                        <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}} border-bottom pv-company-group-{{$key}}">
                                        @foreach($data as $rowData)
                                            <div class="{{$class}}">{{$rowData}}</div>
                                        @endforeach
                                        </td>
                                    @else
                                        <td class="{{"$topBorder $borderLeft $bottomRight"}} border-bottom pv-company-group-{{$key}}">
                                            <div class="data">
                                                <span class="{{$class}}">
                                                    {{$data}}
                                                </span>
                                            </div>
                                            @if(strtolower($heading) == 'company_name')
                                                <div class="expand-toggle mt-3">
                                                    <button class="btn btn-sm btn-default hide-rows" title="Show POC of {{$data}}" data-company-id="{{$key}}" onclick="toggleButton(this)" data-toggle="collapse" data-target=".collapse-{{$key}}">
                                                        <i class="fa fa-plus" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </tr>
                    @if(isset($pvFilterData['poc_data'][$key]) && $pvFilterData['poc_data'][$key])
                        @foreach($pvFilterData['poc_data'][$key] as $pocData)
                            <tr class="collapse collapse-{{$key}}">
                                @if($pocData && count($pocData))
                                    @foreach($pocData as $heading => $data)
                                        @php
                                            $class = (isset($classData[$heading])) ? $classData[$heading] : '';
                                            //$data = ($data) ? $data : '-';
                                            $topBorder = ($key == 'heading') ? 'border-top' : '';
                                            $bottomBorder = ($key == 'heading' || $i == $totalCount) ? 'border-bottom' : '';
                                            $bottomRight = (in_array($heading, ['company_name', 'unique_req_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'bdm_count', 'bdm_wise_count'])) ? 'border-right' : '';
                                            $borderLeft = ($heading == 'company_name') ? 'border-left' : '';
                                        @endphp
                                        @if(strtolower($key) == 'heading')
                                            <th class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}}"><span>{{$data}}</span></th>
                                        @else
                                            @if(is_array($data))
                                                <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}} pv-company-group-{{$key}}">
                                                    @foreach($data as $rowData)
                                                        <div class="{{$class}}">{{$rowData}}</div>
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}} pv-company-group-{{$key}}"><span class="{{$class}}">{{$data}}</span></td>
                                            @endif
                                        @endif
                                    @endforeach
                                @endif
                            </tr>
                        @endforeach
                    @else
                        @if(strtolower($key) != 'heading')
                            <tr class="collapse collapse-{{$key}}">
                                <td class="element-border text-danger text-bold pv-company-group-{{$key}}" colspan="{{count($pvCompanyData)}}">No POC Data Found.</td>
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
