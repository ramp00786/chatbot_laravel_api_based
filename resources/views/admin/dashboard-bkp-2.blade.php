@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="h3 mb-0">Chatbot Analytics Dashboard</h1>
            <small class="text-muted">Last updated: {{ now()->format('M d, Y H:i') }}</small>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <!-- Total Sessions -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Active Sessions -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Active Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $activeSessions }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Messages -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Total Messages</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalMessages }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Locations -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Unique Locations</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $uniqueLocations }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Devices -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Desktop Users</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $desktopUsers }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-desktop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                                Today's Visitors</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $todaysVisitors }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <!-- Sessions Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sessions Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                            <div class="dropdown-header">View Options:</div>
                            <a class="dropdown-item" href="#" onclick="updateChart('sessionsChart', 'week')">Last 7 Days</a>
                            <a class="dropdown-item" href="#" onclick="updateChart('sessionsChart', 'month')">Last 30 Days</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="sessionsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages Chart -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Messages Trend</h6>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="messagesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Reports Row -->
    <div class="row">
        <!-- Locations -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Locations</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="locationsChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        @foreach($topLocations as $location)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ $location['color'] }}"></i> {{ $location['name'] }}
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Devices -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Device Distribution</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="devicesChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Desktop
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Mobile
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-info"></i> Tablet
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Traffic Sources -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Traffic Sources</h6>
                </div>
                <div class="card-body">
                    <div class="chart-pie pt-4 pb-2">
                        <canvas id="trafficChart"></canvas>
                    </div>
                    <div class="mt-4 text-center small">
                        <span class="mr-2">
                            <i class="fas fa-circle text-warning"></i> Direct
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-danger"></i> Referral
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-secondary"></i> Social
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Chat Sessions</h6>
                    <a href="{{ route('admin.chat.history') }}" class="btn btn-sm btn-primary">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                            <thead class="thead-light">
                                <tr>
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Started At</th>
                                    <th>Duration</th>
                                    <th>Messages</th>
                                    <th>Location</th>
                                    <th>Device</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentSessions as $session)
                                <tr>
                                    <td>{{ $session->user_name }}</td>
                                    <td>{{ $session->user_email }}</td>
                                    <td>{{ $session->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($session->ended_at)
                                            {{ $session->created_at->diff($session->ended_at)->format('%Hh %Im') }}
                                        @else
                                            Active
                                        @endif
                                    </td>
                                    <td>{{ $session->messages_count }}</td>
                                    <td>{{ $session->location ?? 'Unknown' }}</td>
                                    <td>{{ $session->device_type ?? 'Desktop' }}</td>
                                    <td>
                                        <a href="{{ route('admin.chat.history.show', $session) }}" 
                                           class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link href="{{ asset('vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
@endpush

@push('scripts')
<!-- Chart.js -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
// Sessions Chart
var ctx = document.getElementById('sessionsChart');
var sessionsChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($sessionsChart['labels']) !!},
        datasets: [{
            label: 'Sessions',
            data: {!! json_encode($sessionsChart['data']) !!},
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

// Messages Chart
var ctx = document.getElementById('messagesChart');
var messagesChart = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($messagesChart['labels']) !!},
        datasets: [{
            label: 'Messages',
            data: {!! json_encode($messagesChart['data']) !!},
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
                beginAtZero: true,
                ticks: {
                    precision: 0
                }
            }
        }
    }
});

// Locations Chart
var ctx = document.getElementById('locationsChart');
var locationsChart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($topLocations->pluck('name')) !!},
        datasets: [{
            data: {!! json_encode($topLocations->pluck('count')) !!},
            backgroundColor: {!! json_encode($topLocations->pluck('color')) !!},
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
    },
});

// Devices Chart
var ctx = document.getElementById('devicesChart');
var devicesChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Desktop', 'Mobile', 'Tablet'],
        datasets: [{
            data: {!! json_encode($deviceDistribution) !!},
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    },
});

// Traffic Chart
var ctx = document.getElementById('trafficChart');
var trafficChart = new Chart(ctx, {
    type: 'polarArea',
    data: {
        labels: ['Direct', 'Referral', 'Social', 'Organic'],
        datasets: [{
            data: {!! json_encode($trafficSources) !!},
            backgroundColor: ['#f6c23e', '#e74a3b', '#858796', '#36b9cc'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        }
    },
});

// Update chart data
function updateChart(chartId, range) {
    fetch(`/admin/dashboard/chart-data/${chartId}/${range}`)
        .then(response => response.json())
        .then(data => {
            const chart = window[chartId];
            chart.data.labels = data.labels;
            chart.data.datasets[0].data = data.data;
            chart.update();
        });
}

// Initialize DataTable
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[2, "desc"]],
        "pageLength": 5
    });
});
</script>
@endpush