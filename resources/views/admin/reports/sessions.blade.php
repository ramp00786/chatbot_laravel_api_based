
@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ $title }}</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Export Report
        </a>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Session Details</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Session ID</th>
                            <th>User</th>
                            <th>Email</th>
                            <th>Start Time</th>
                            <th>Duration</th>
                            <th>Chats</th>
                            <th>Location</th>
                            <th>Device</th>
                            <th>API Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        
                        @foreach($sessions as $session)
                        <tr>
                            <td>{{ $session->id }}</td>
                            <td>{{ $session->user_name }}</td>
                            <td> {{ $session->user_email }}</td>
                            <td>{{ $session->created_at->format('M d, Y H:i') }}</td>
                            <td>
                                @if($session->ended_at)
                                {{ $session->created_at->diff($session->ended_at)->format('%Hh %Im %Ss') }}
                                    
                                @else
                                <span class="badge bg-success">Active</span>
                                @endif
                            </td>
                            <td>
                               
                               
                                
                                <a href="{{route('admin.dashboard.session_id', $session->id)}}" 
                                class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>  {{ $session->messages->count() }}
                                </a>
                            </td>
                            <td>{{ $session->location ?? 'Unknown' }}</td>
                            <td>{{ $session->device_type ?? 'Unknown' }}</td>
                            <td>{{ $session->apiKey->name ?? 'N/A' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $sessions->links() }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Sessions Over Time</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="sessionsTimelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Session Duration Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="durationDistributionChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')

{{-- Chart.js --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    

<!-- Bootstrap 5.3.0 Bundle (includes Popper.js) -->
{{-- <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script> --}}

<!-- DataTables 2.2.2 -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.js"></script>

<!-- DataTables Bootstrap 5 integration -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.bootstrap5.js"></script>
<!-- ECharts for advanced visualizations -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/echarts/5.6.0/echarts.min.js" integrity="sha512-XSmbX3mhrD2ix5fXPTRQb2FwK22sRMVQTpBP2ac8hX7Dh/605hA2QDegVWiAvZPiXIxOV0CbkmUjGionDpbCmw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script>
// Sessions timeline chart
var ctx = document.getElementById('sessionsTimelineChart');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($last30Days) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode($sessionsCount) !!},
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderColor: 'rgba(78, 115, 223, 1)',
            pointRadius: 3,
            pointBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointBorderColor: '#fff',
            pointHoverRadius: 3,
            pointHoverBackgroundColor: 'rgba(78, 115, 223, 1)',
            pointHoverBorderColor: '#fff',
            pointHitRadius: 10,
            pointBorderWidth: 2,
            tension: 0.3,
            fill: true
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true
            }
        }
    }
});

// Duration distribution chart
var ctx = document.getElementById('durationDistributionChart');
var chart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: ['<1 min', '1-5 min', '5-15 min', '15-30 min', '30+ min'],
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode($durationDistribution) !!},
            backgroundColor: 'rgba(54, 185, 204, 0.5)',
            borderColor: 'rgba(54, 185, 204, 1)',
            borderWidth: 1
        }]
    },
    options: {
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: {
                    display: false
                }
            },
            y: {
                beginAtZero: true
            }
        }
    }
});

// Initialize DataTable
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[2, "desc"]],
        "pageLength": 10
    });
    
    // Update real-time metrics every 5 seconds
    // updateRealTimeMetrics();
    // setInterval(updateRealTimeMetrics, 5000);
});


</script>
@endpush