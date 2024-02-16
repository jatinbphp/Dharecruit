@extends('admin.layouts.app')
@section('content')
    <div class="content-wrapper">
        <section class="content">
            <div class="container-fluid">
                @if($loggedUser['role'] == 'admin')
                    <div class="row">
                        <div class="col-lg-3 col-6 mt-3">
                            <div class="small-box bg-warning">
                                <div class="inner">
                                    <h3>{{$users}}</h3>
                                    <p>Total Admin</p>
                                </div>
                                <div class="icon">
                                    <i class="fa fa-users"></i>
                                </div>
                                <a href="{{route('user.index')}}" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="requirementChart">
                            @include('admin.chart.requirement')
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
@endsection
@section('jquery')
<script type="text/javascript">
    $(document).ready(function () {
        var data = {
            labels: {!! json_encode($monthlyLabels) !!},
            requirementCounts: {!! json_encode($monthlyRequiremtCounts) !!},
            submissionCounts: {!! json_encode($monthlySubmissionCounts) !!},
        };

        var ctx = document.getElementById('barChart').getContext('2d');
        var barChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Requirement Count',
                    data: data.requirementCounts,
                    backgroundColor: '#688ade',
                    borderColor: '#0013b0',
                    borderWidth: 1
                }, {
                    label: 'Submission Count',
                    data: data.submissionCounts,
                    backgroundColor: '#dc7979',
                    borderColor: '#ff0000',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                legend: {
                    display: true,
                    position: 'bottom', // Place legend at the bottom
                },
            }
        });

        $('input[type="radio"]').on('change', function() {
            var type = $(this).attr('data-type');
            if(!type){
                return;
            }

            $.ajax({
                url: "{{ route('getChartData') }}",
                method: 'POST',
                data: {
                    '_token' : '{{ csrf_token() }}',
                    'type'   : type,
                },
                success: function(response) {
                    console.log(response);
                    if(response.status == 1){
                        barChart.data.labels = response.label;
                        barChart.data.datasets[0].data = response.requiremtCounts;
                        barChart.data.datasets[1].data = response.submissionCounts;
                        barChart.update();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching data:', error);
                }
            });
            // // Fetch updated data based on the selected option via Ajax
            // var updatedData = fetchUpdatedData(selectedOption);
            //
            // // Update chart data
            // barChart.data.labels = updatedData.labels;
            // barChart.data.datasets[0].data = updatedData.requirementCounts;
            // barChart.data.datasets[1].data = updatedData.submissionCounts;
            //
            // // Update chart options if needed
            //
            // // Update chart
            // barChart.update();
        });
    });
</script>
@endsection
