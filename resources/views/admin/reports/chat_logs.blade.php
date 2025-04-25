@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chat Logs</h1>
        <div>
            <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm" id="exportReport">
                <i class="fas fa-download fa-sm text-white-50"></i> Export Logs
            </a>
        </div>
    </div>

    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">Chat Logs Overview</h6>
            <div class="dropdown no-arrow">
                <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                </a>
                <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                    aria-labelledby="dropdownMenuLink">
                    <div class="dropdown-header">Filter Options:</div>
                    <a class="dropdown-item" href="{{ route('admin.reports.chat_logs') }}?type=all">All Logs</a>
                    <a class="dropdown-item" href="{{ route('admin.reports.chat_logs') }}?type=user">User Messages</a>
                    <a class="dropdown-item" href="{{ route('admin.reports.chat_logs') }}?type=bot">Bot Responses</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#" id="filterToday">Today</a>
                    <a class="dropdown-item" href="#" id="filterWeek">Last 7 Days</a>
                    <a class="dropdown-item" href="#" id="filterMonth">Last 30 Days</a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="chatLogsTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Session</th>
                            <th>Type</th>
                            <th>Sender</th>
                            <th>Message</th>
                            <th>Timestamp</th>
                            <th>Response Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($chatLogs as $log)
                        <tr>
                            <td>{{ $log->id }}</td>
                            <td>
                                <a href="{{ route('admin.chat.history.show', $log->session) }}">
                                    #{{ $log->session->id }}
                                </a>
                            </td>
                            <td>{{ $log->type ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ $log->sender === 'bot' ? 'info' : 'primary' }}">
                                    {{ ucfirst($log->sender) }}
                                </span>
                            </td>
                            <td>{{ Str::limit($log->message, 100) }}</td>
                            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
                            <td>
                                @if($log->sender === 'bot')
                                    @php
                                        $userMessage = $log->session->chatLogs
                                            ->where('created_at', '<', $log->created_at)
                                            ->where('sender', 'user')
                                            ->sortByDesc('created_at')
                                            ->first();
                                    @endphp
                                    @if($userMessage)
                                        {{ $log->created_at->diffInSeconds($userMessage->created_at) }}s
                                    @else
                                        -
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $chatLogs->links() }}
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chat Logs Over Time</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="chatLogsTimelineChart"></canvas>
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
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
// Chat logs timeline chart
var ctx = document.getElementById('chatLogsTimelineChart');
var chart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: @json($timelineLabels),
        datasets: [{
            label: 'Chat Logs',
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
            data: @json([$userLogsCount, $botLogsCount]),
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
    $('#chatLogsTable').DataTable({
        "order": [[5, "desc"]],
        "pageLength": 25,
        "responsive": true
    });
});

// Filter buttons
document.getElementById('filterToday').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.chat_logs') }}?date_range=today";
});

document.getElementById('filterWeek').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.chat_logs') }}?date_range=week";
});

document.getElementById('filterMonth').addEventListener('click', function(e) {
    e.preventDefault();
    window.location.href = "{{ route('admin.reports.chat_logs') }}?date_range=month";
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