@if(isset($pocFilterData) && $pocFilterData && count($pocFilterData) && isset($pocFilterData['poc_data']) && count($pocFilterData['poc_data']))
    @php
        $classData = isset($pocFilterData['class_data']) ? $pocFilterData['class_data'] : [];
    @endphp
    <div class="col-md-12 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped">
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border" colspan="2">Company Name</th>
                    <th scope="col" colspan="4" class="text-center element-border">Requirement</th>
                    <th scope="col" colspan="5" class="text-center element-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border">Vendor Status</th>
                    <th scope="col" colspan="8" class="text-center element-border">Client Status</th>
                    <th scope="col" colspan="2" class="text-center element-border">BDM / Category</th>
                </tr>
                </thead>
                <tbody>
                    @if(isset($pocFilterData['heading']) && count($pocFilterData['heading']))
                        <tr>
                            @foreach($pocFilterData['heading'] as $key => $heading)
                                @php
                                    $bottomRight = (in_array($key, ['poc_name', 'unique_req_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'bdm_wise_count'])) ? 'border-right' : '';
                                @endphp
                                <th class="border-bottom border-left {{"$bottomRight"}}">{{$heading}}</th>
                            @endforeach
                        </tr>
                    @endif
                    @php
                        $totalCount = count($pocFilterData['poc_data']);
                        $i = 1;
                    @endphp
                    @foreach($pocFilterData['poc_data'] as $companyKey => $allPocData)
                        @if(count($allPocData))
                            @php
                                $allPocCount = count($allPocData);
                                $pocCount = 1;
                            @endphp
                            @foreach($allPocData as $pocData)
                                @if(count($pocData))
                                    <tr>
                                        @foreach($pocData as $heading => $data)
                                            @php
                                                $class = (isset($classData[$heading])) ? $classData[$heading] : '';
                                                //$data = ($data) ? $data : '-'
                                                $bottomRight = (in_array($heading, ['poc_name', 'unique_req_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'bdm_wise_count'])) ? 'border-right' : '';
                                                $borderLeft = $heading == 'vendor_company_name' ? 'border-left' : '';
                                            @endphp
                                            @if(is_array($data))
                                                <td class="{{"$borderLeft $bottomRight"}} border-bottom">
                                                    @foreach($data as $rowData)
                                                        <div class="{{$class}}">{{$rowData}}</div>
                                                    @endforeach
                                                </td>
                                            @else
                                                @php
                                                    $rowSpan = 0;
                                                    if($heading == 'vendor_company_name' && $pocCount == 1){
                                                        $rowSpan = $allPocCount;
                                                    }
                                                @endphp
                                                @if(!$rowSpan && $heading == 'vendor_company_name')
                                                    @continue;
                                                @endif
                                                <td @if($rowSpan) rowspan="{{$rowSpan}}" @endif class="{{"$borderLeft $bottomRight"}} border-bottom">
                                                    <span class="{{$class}}">{{$data}}</span>
                                                </td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endif
                                @php $pocCount++; @endphp
                            @endforeach
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
