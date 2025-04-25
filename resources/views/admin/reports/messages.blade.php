@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Messages Report</h1>
        <div>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="exportReport">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Report
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Messages Overview</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Filter Options:</div>
                    <a class="dropdown-item" href="{{ route('admin.reports.messages') }}?type=all">All Messages</a>
                    <a class="dropdown-item" href="{{ route('admin.reports.messages') }}?type=user">User Messages</a>
                    <a class="dropdown-item" href="{{ route('admin.reports.messages') }}?type=bot">Bot Responses</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="filterToday">Today</a>
                    <a class="dropdown-item" href="#" id="filterWeek">Last 7 Days</a>
                    <a class="dropdown-item" href="#" id="filterMonth">Last 30 Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="messagesTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Session</th>
                            <th>User</th>
                            <th>Message</th>
                            <th>Type</th>
                            <th>Timestamp</th>
                            <th>Response Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                        <tr>
                            <td>{{ $message->id }}</td>
                            <td>
                                <a href="{{ route('admin.dashboard.session_id', $message->chatSession) }}">
                                    #{{ $message->chatSession->id }}
                                </a>
                            </td>
                            <td>{{ $message->chatSession->user_name }}</td>
                            <td>
                                @if(strlen($message->content) > 50)
                                    {{ substr($message->content, 0, 50) }}...
                                @else
                                    {{ $message->content }}
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $message->is_bot ? 'info' : 'primary' }}">
                                    {{ $message->is_bot ? 'Bot' : 'User' }}
                                </span>
                            </td>
                            <td>{{ $message->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                @if($message->is_bot && $message->chatSession->messages->where('created_at', '<', $message->created_at)->where('is_bot', false)->first())
                                    {{ $message->created_at->diffInSeconds($message->chatSession->messages->where('created_at', '<', $message->created_at)->where('is_bot', false)->first()->created_at) }}s
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $messages->links() }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Messages Over Time</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="messagesTimelineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Message Types Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="messageTypesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> User Messages
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Bot Responses
                        </span>
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
// Messages timeline chart
var ctx = document.getElementById('messagesTimelineChart');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($timelineLabels),
        datasets: [{
            label: 'Messages',
            data: @json($timelineData),
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
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Message types chart
var ctx = document.getElementById('messageTypesChart');
var chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: ['User Messages', 'Bot Responses'],
        datasets: [{
            data: @json([$userMessagesCount, $botMessagesCount]),
            backgroundColor: ['#4e73df', '#36b9cc'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        cutout: '70%',
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Initialize DataTable
$(document).ready(function() {
    $('#messagesTable').DataTable({
        "order": [[5, "desc"]],
        "pageLength": 25,
        "responsive": true
    });
});

// Filter buttons
document.getElementById('filterToday').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.messages') }}?date_range=today";
});

document.getElementById('filterWeek').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.messages') }}?date_range=week";
});

document.getElementById('filterMonth').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.messages') }}?date_range=month";
});

// Export functionality
document.getElementById('exportReport').addEventListener('click', function() {
    // Implement export functionality here
    alert('Export functionality would be implemented here');
});
</script>
@endpush

@push('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
<style>
    .chart-area {
        position: relative;
        height: 300px;
    }
    .chart-pie {
        position: relative;
        height: 250px;
    }
    .badge {
        font-size: 85%;
        font-weight: 500;
    }
</style>
@endpush