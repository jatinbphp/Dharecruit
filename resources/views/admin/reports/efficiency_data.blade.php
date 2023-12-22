@if(isset($bdmsData) && $bdmsData && count($bdmsData))
    @if(isset($bdmsData['user_data']) && count($bdmsData['user_data']))
        @php
            $classData = isset($bdmsData['class_data']) ? $bdmsData['class_data'] : [];
         @endphp
        <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="BDM">
            @foreach($bdmsData['user_data'] as $userId => $bdmData)
                <div class="table-responsive m-lg-n2">
                    <table class="table table-bordered">
                        <caption class="text-bold py-0">
                            <span class="badge badge-info my-2 p-2">BDM: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">BDM</th>
                            <th scope="col" colspan="5" class="text-center">Requirement</th>
                            <th scope="col" class="text-center">Submission</th>
                            <th scope="col" colspan="4" class="text-center">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center">Vendor Status</th>
                            <th scope="col" colspan="6" class="text-center">Client Status</th>
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
                                                @endphp
                                                @if(strtolower($key) == 'heading')
                                                    <th class="{{$class}}">{{$data}}</th>
                                                @else
                                                    <td class="{{$class}}">{{$data}}</td>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
          @endforeach
        </div>
    @endif
@endif
@if(isset($recruitersData) && $recruitersData && count($recruitersData))
    @if(isset($recruitersData['user_data']) && count($recruitersData['user_data']))
        @php
            $classData = isset($recruitersData['class_data']) ? $recruitersData['class_data'] : [];
        @endphp
        <div class="col-md-12 mt-3 p-3 border border-with-label" data-label="Recruiter">
            @foreach($recruitersData['user_data'] as $userId => $recruitersData)
                <div class="table-responsive m-lg-n2">
                    <table class="table table-bordered">
                        <caption class="text-bold py-0">
                            <span class="badge badge-info my-2 p-2">Recruiter: {{\App\Models\Admin::getUserNameBasedOnId($userId)}}</span>
                        </caption>
                        <thead>
                        <tr>
                            <th scope="col" class="text-center">Recruiter</th>
                            <th scope="col" colspan="4" class="text-center">Requirement</th>
                            <th scope="col" colspan="2" class="text-center">Submission</th>
                            <th scope="col" colspan="4" class="text-center">BDM Status</th>
                            <th scope="col" colspan="5" class="text-center">Vendor Status</th>
                            <th scope="col" colspan="6" class="text-center">Client Status</th>
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
                                            @endphp
                                            @if(strtolower($key) == 'heading')
                                                <th class="{{$class}}">{{$data}}</th>
                                            @else
                                                <td class="{{$class}}">{{$data}}</td>
                                            @endif
                                        @endforeach
                                    @endif
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            @endforeach
        </div>
    @endif
@endif
