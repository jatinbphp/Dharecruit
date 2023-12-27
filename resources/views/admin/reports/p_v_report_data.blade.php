@if(isset($pvFilterData) && $pvFilterData && count($pvFilterData) && isset($pvFilterData['user_data']) && count($pvFilterData['user_data']))
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
                    $totalCount = count($pvFilterData['user_data']);
                    $i = 1;
                @endphp
                @foreach($pvFilterData['user_data'] as $key => $pvCompanyData)
                    <tr>
                        @if($pvCompanyData && count($pvCompanyData))
                            @foreach($pvCompanyData as $heading => $data)
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
                                    <td class="{{"$topBorder $bottomBorder $borderLeft $bottomRight"}}"><span class="{{$class}}">{{$data}}</span></td>
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
