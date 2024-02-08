@if(isset($pocFilterData) && $pocFilterData && count($pocFilterData) && isset($pocFilterData['poc_data']) && count($pocFilterData['poc_data']))
    @php
        $classData    = isset($pocFilterData['class_data']) ? $pocFilterData['class_data'] : [];
        $emptyPOCRows = isset($pocFilterData['empty_poc_rows']) ? $pocFilterData['empty_poc_rows'] : [];
        $hideColumns  = isset($pocFilterData['hide_columns']) ? $pocFilterData['hide_columns'] : [];
        $pvCompanyWiseOrgReqCount  = isset($pocFilterData['pv_company_org_req_count']) ? $pocFilterData['pv_company_org_req_count'] : [];
        $pocWiseOrgReqCount        = isset($pocFilterData['poc_org_req_count']) ? $pocFilterData['poc_org_req_count'] : [];
        $isNewPvCompany            = isset($pocFilterData['is_new_pv_company']) ? $pocFilterData['is_new_pv_company'] : [];
        $isNewPoc                  = isset($pocFilterData['is_new_poc']) ? $pocFilterData['is_new_poc'] : [];
    @endphp
    <div class="col-md-12 p-3 border border-with-label" data-label="">
        <div class="table-responsive m-lg-n2">
            <table class="table table-bordered table-striped" id="poc_report">
                <thead>
                <tr>
                    <th scope="col" class="text-center element-border company-name" colspan="5">Company Name</th>
                    <th scope="col" colspan="4" class="text-center element-border rm-left-border">Requirement</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">BDM Status</th>
                    <th scope="col" colspan="5" class="text-center element-border rm-left-border">Vendor Status</th>
                    <th scope="col" colspan="8" class="text-center element-border rm-left-border">Client Status</th>
                    <th scope="col" colspan="2" class="text-center element-border rm-left-border">BDM / Category</th>
                </tr>
                @if(isset($pocFilterData['heading']) && count($pocFilterData['heading']))
                    <tr>
                        @foreach($pocFilterData['heading'] as $key => $heading)
                            @php
                                $borderleft = ($key == 'who_added')  ? 'border-left' : '';
                            @endphp
                            <th class='border-bottom border-right {{"$borderleft $key"}}'>{{$heading}}</th>
                        @endforeach
                    </tr>
                @endif
                </thead>
                <tbody>
                    @foreach($pocFilterData['poc_data'] as $companyKey => $allPocData)
                        @if(count($allPocData))
                            @php
                                $allPocCount = count($allPocData);
                                $pocCount = 1;
                            @endphp
                            @foreach($allPocData as $pocName => $pocData)
                                @if(count($pocData))
                                    <tr class=" {{$pocName}} @if(in_array($companyKey.'_'.$pocName, $emptyPOCRows)) empty-row @endif">
                                        @foreach($pocData as $heading => $data)
                                            @php
                                                $data = ($data) ? $data : '';
                                                $class = (isset($classData[$heading]) && $data) ? $classData[$heading] : '';
                                                $bottomRight = (in_array($heading, ['poc_name', 'unique_req_count', 'status_unviewed', 'status_position_closed', 'client_status_total', 'bdm_wise_count'])) ? 'border-right' : '';
                                                $borderLeft = $heading == 'who_added' ? 'border-left' : '';
                                            @endphp
                                            @if(is_array($data))
                                                <td class="{{"$borderLeft $bottomRight $heading"}} border-bottom">
                                                    @foreach($data as $rowData)
                                                        <div class="{{$class}}">{{$rowData}}</div>
                                                    @endforeach
                                                </td>
                                            @else
                                                {{--@php
                                                    $rowSpan = 0;
                                                    if($heading == 'vendor_company_name' && $pocCount == 1){
                                                        $rowSpan = $allPocCount;
                                                    }
                                                @endphp
                                                @if(!$rowSpan && $heading == 'vendor_company_name')
                                                    @continue;
                                                @endif--}}
                                                <td {{--@if($rowSpan) rowspan="{{$rowSpan}}" @endif--}} class="{{"$borderLeft $bottomRight  $heading"}} border-bottom">
                                                    @if($heading == 'vendor_company_name')
                                                        <div class="pr-3 text-right">
                                                            <span class="badge bg-indigo position-absolute top-0 end-0 show-count" style="margin-top: -6px">{{isset($pvCompanyWiseOrgReqCount[$data]) ? $pvCompanyWiseOrgReqCount[$data] : 0}}</span>
                                                            <p class="{{$class}} text-left @if(isset($isNewPvCompany[$companyKey]) && $isNewPvCompany[$companyKey] == 1) text-primary @endif">{{$data}}</p>
                                                        </div>
                                                    @elseif($heading == 'poc_name')
                                                        <div class="pr-3 text-right">
                                                            <span class="badge bg-indigo position-absolute top-0 end-0 show-count" style="margin-top: -6px">{{isset($pocWiseOrgReqCount[$companyKey][$data]) ? $pocWiseOrgReqCount[$companyKey][$data] : 0}}</span>
                                                            <p class="{{$class}} text-left @if(isset($isNewPoc[$companyKey][$data]) && $isNewPoc[$companyKey][$data] == 1) text-primary @endif">{{$data}}</p>
                                                        </div>
                                                    @else
                                                        <span class="{{$class}}">{{$data}}</span>
                                                    @endif
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
