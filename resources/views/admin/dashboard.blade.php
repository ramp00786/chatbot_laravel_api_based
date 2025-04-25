@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Chatbot Analytics Dashboard</h1>
        <div class="d-flex">

            {{-- <div class="dropdown">
                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button"
                        id="timeRangeDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="far fa-calendar-alt"></i> <span id="selectedTimeRange">Last 30 Days</span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="timeRangeDropdown">
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('1')">Last 1 Day</a></li>
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('3')">Last 3 Days</a></li>
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('7')">Last 7 Days</a></li>
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('15')">Last 15 Days</a></li>
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('30')">Last 30 Days</a></li>
                  <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateTimeRange('90')">Last 90 Days</a></li>
                </ul>
              </div> --}}

            
            
        </div>
    </div>


    <!-- KPI Cards -->
    <div class="row">
        <!-- Total Sessions -->
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total Sessions</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_sessions'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['total_sessions'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['total_sessions'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($changes['total_sessions']) }}%
                                </span>
                                <span class="text-muted">vs last period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-comments fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.sessions') }}" class="stretched-link"></a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_sessions'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['active_sessions'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['active_sessions'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($changes['active_sessions']) }}%
                                </span>
                                <span class="text-muted">vs last period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bolt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.sessions', ['type' => 'active']) }}" class="stretched-link"></a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_messages'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['total_messages'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['total_messages'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($changes['total_messages']) }}%
                                </span>
                                <span class="text-muted">vs last period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-envelope fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.messages') }}" class="stretched-link"></a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['unique_locations'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['unique_locations'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['unique_locations'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($changes['unique_locations']) }}%
                                </span>
                                <span class="text-muted">vs last period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-globe fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.locations') }}" class="stretched-link"></a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['desktop_users'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['desktop_users'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['desktop_users'] >= 0 ? 'up' : 'down' }}"></i> {{ abs($changes['desktop_users']) }}%
                                </span>
                                <span class="text-muted">vs last period</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-desktop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.devices') }}" class="stretched-link"></a>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['todays_visitors'] }}</div>
                            <div class="mt-2 text-xs">
                                <span class="{{ $changes['todays_visitors'] >= 0 ? 'text-success' : 'text-danger' }} mr-1">
                                    <i class="fas fa-arrow-{{ $changes['todays_visitors'] >= 0 ? 'up' : 'down' }}"></i> {{ round(abs($changes['todays_visitors']), 2) }}%
                                </span>
                                <span class="text-muted">vs yesterday</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <a href="{{ route('admin.reports.analytics') }}" class="stretched-link"></a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Charts Row -->
    <div class="row">
        <!-- Sessions Overview -->
        <div class="col-xl-12 col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Sessions Overview</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                          <li><h6 class="dropdown-header">View Options:</h6></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('sessionsChart', 'week')">Last 7 Days</a></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('sessionsChart', 'month')">Last 30 Days</a></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('sessionsChart', 'quarter')">Last 90 Days</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="{{ route('admin.reports.sessions') }}">View Full Report</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="sessionsChart"></canvas>
                    </div>
                    <div class="mt-4 small text-muted">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Sessions
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Messages
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Real-Time Metrics -->
        {{-- <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Real-Time Metrics</h6>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">Server Load <span class="float-right" id="serverLoad">20%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-success" id="serverLoadBar" role="progressbar" style="width: 20%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">API Usage <span class="float-right" id="apiUsage">40%</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-info" id="apiUsageBar" role="progressbar" style="width: 40%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">Response Time <span class="float-right" id="responseTime">500ms</span></h4>
                        <div class="progress mb-4">
                            <div class="progress-bar bg-warning" id="responseTimeBar" role="progressbar" style="width: 50%"></div>
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <h4 class="small font-weight-bold">Active Connections <span class="float-right" id="activeConnections">{{ $stats['active_sessions'] }}</span></h4>
                        <div class="progress">
                            <div class="progress-bar bg-danger" id="activeConnectionsBar" role="progressbar" 
                                style="width: {{ min($stats['active_sessions'] * 5, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Second Row -->
    <div class="row">
        <!-- Messages Trend -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Messages Trend</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                          <li><h6 class="dropdown-header">View Options:</h6></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('messagesChart', 'week')">Last 7 Days</a></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('messagesChart', 'month')">Last 30 Days</a></li>
                          {{-- <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateChart('messagesChart', 'quarter')">Last 90 Days</a></li> --}}
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item" href="{{ route('admin.reports.messages') }}">View Full Report</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="messagesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Engagement -->
        <div class="col-xl-6 col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">User Engagement</h6>
                    <div class="dropdown no-arrow">
                        <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink"
                           data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="fas fa-ellipsis-v fa-sm fa-fw text-gray-400"></i>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow animated--fade-in"
                            aria-labelledby="dropdownMenuLink">
                          <li><h6 class="dropdown-header">View Options:</h6></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateEngagementChart('week')">Last 7 Days</a></li>
                          <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateEngagementChart('month')">Last 30 Days</a></li>
                          {{-- <li><a class="dropdown-item" href="#" onclick="event.preventDefault(); updateEngagementChart('quarter')">Last 90 Days</a></li> --}}
                        </ul>
                      </div>
                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas id="engagementChart"></canvas>
                    </div>
                    <div class="mt-4 small text-muted">
                        <span class="mr-2">
                            <i class="fas fa-circle text-primary"></i> Avg. Session Duration
                        </span>
                        <span class="mr-2">
                            <i class="fas fa-circle text-success"></i> Messages per Session
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Row - Distribution Charts -->
    <div class="row">
        <!-- Locations -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">Top Locations</h6>
                    <a href="{{ route('admin.reports.locations') }}" class="btn btn-sm btn-link">View All</a>
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
                    <a href="{{ route('admin.reports.devices') }}" class="btn btn-sm btn-link">View All</a>
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
                    <a href="{{ route('admin.reports.analytics') }}" class="btn btn-sm btn-link">View All</a>
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
                    <div>
                        <a href="{{ route('admin.reports.sessions') }}" class="btn btn-sm btn-primary">
                            View All <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                    </div>
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
                                            <span class="badge bg-success">Active</span>
                                        @endif
                                    </td>
                                    <td>{{ $session->messages_count }}</td>
                                    <td>{{ $session->location ?? 'Unknown' }}</td>
                                    <td>{{ $session->device_type ?? 'Desktop' }}</td>
                                    <td>
                                        <a href="{{route('admin.dashboard.session_id', $session->id)}}" 
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
<link href="https://cdn.datatables.net/2.2.2/css/dataTables.bootstrap5.css" rel="stylesheet">
<style>
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .progress-bar {
        border-radius: 5px;
    }
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


@push('scripts')
<!-- Chart.js -->
{{-- <script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>
<!-- DataTables -->
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
<!-- ECharts for advanced visualizations -->
<script src="{{ asset('vendor/echarts/echarts.min.js') }}"></script> --}}

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
        },
        plugins: {
            tooltip: {
                mode: 'index',
                intersect: false,
            },
            legend: {
                display: false
            }
        },
        hover: {
            mode: 'nearest',
            intersect: true
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

// Engagement Chart
var ctx = document.getElementById('engagementChart');
var engagementChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($engagementChart['labels']) !!},
        datasets: [
            {
                label: 'Avg. Session Duration (min)',
                data: {!! json_encode($engagementChart['duration']) !!},
                borderColor: 'rgba(78, 115, 223, 1)',
                backgroundColor: 'rgba(78, 115, 223, 0.05)',
                yAxisID: 'y',
                tension: 0.3,
                fill: true
            },
            {
                label: 'Messages per Session',
                data: {!! json_encode($engagementChart['messages']) !!},
                borderColor: 'rgba(28, 200, 138, 1)',
                backgroundColor: 'rgba(28, 200, 138, 0.05)',
                yAxisID: 'y1',
                tension: 0.3,
                fill: true
            }
        ]
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
                type: 'linear',
                display: true,
                position: 'left',
                title: {
                    display: true,
                    text: 'Duration (min)'
                }
            },
            y1: {
                type: 'linear',
                display: true,
                position: 'right',
                title: {
                    display: true,
                    text: 'Messages'
                },
                grid: {
                    drawOnChartArea: false
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

// Devices Chart "{"Desktop":14,"Mobile":0,"Tablet":0}"
var ctx = document.getElementById('devicesChart');
var devicesChart = new Chart(ctx, {
    type: 'pie',
    data: {
        labels: ['Desktop', 'Mobile', 'Tablet'],
        datasets: [{
            data: [{{$deviceDistribution['Desktop']}},{{$deviceDistribution['Mobile']}},{{$deviceDistribution['Tablet']}}],
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
            data: [{{$deviceDistribution['Desktop']}},{{$deviceDistribution['Mobile']}},{{$deviceDistribution['Tablet']}}, 5],
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

// Update engagement chart
function updateEngagementChart(range) {
    fetch(`/admin/dashboard/engagement-data/${range}`)
        .then(response => response.json())
        .then(data => {
            engagementChart.data.labels = data.labels;
            engagementChart.data.datasets[0].data = data.duration;
            engagementChart.data.datasets[1].data = data.messages;
            engagementChart.update();
        });
}

// Update time range
function updateTimeRange(days) {
    fetch(`/admin/dashboard/update-time-range/${days}`)
        .then(response => response.json())
        .then(data => {
            // Update all charts with new data
            sessionsChart.data.labels = data.sessions.labels;
            sessionsChart.data.datasets[0].data = data.sessions.data;
            sessionsChart.update();
            
            messagesChart.data.labels = data.messages.labels;
            messagesChart.data.datasets[0].data = data.messages.data;
            messagesChart.update();
            
            // Update other charts similarly...
            
            // Update the time range button text
            let text = 'Last '+days+' Days';
            if (days === '1') text = 'Last 1 Day';
            // if (days === '90') text = 'Last 90 Days';
            document.getElementById('timeRangeDropdown').innerHTML = `<i class="far fa-calendar-alt"></i> ${text}`;
        });
}

// Simulate real-time metrics updates
function updateRealTimeMetrics() {
    // Simulate server load
    const load = Math.min(100, Math.max(0, 20 + Math.random() * 60));
    document.getElementById('serverLoad').textContent = `${Math.round(load)}%`;
    document.getElementById('serverLoadBar').style.width = `${load}%`;
    document.getElementById('serverLoadBar').className = `progress-bar ${
        load < 50 ? 'bg-success' : load < 80 ? 'bg-warning' : 'bg-danger'
    }`;
    
    // Simulate API usage
    const apiUsage = Math.min(100, Math.max(0, 40 + Math.random() * 40));
    document.getElementById('apiUsage').textContent = `${Math.round(apiUsage)}%`;
    document.getElementById('apiUsageBar').style.width = `${apiUsage}%`;
    
    // Simulate response time
    const responseTime = Math.max(100, Math.random() * 1000);
    document.getElementById('responseTime').textContent = `${Math.round(responseTime)}ms`;
    const responseTimeWidth = Math.min(100, responseTime / 10);
    document.getElementById('responseTimeBar').style.width = `${responseTimeWidth}%`;
    document.getElementById('responseTimeBar').className = `progress-bar ${
        responseTime < 300 ? 'bg-success' : responseTime < 600 ? 'bg-warning' : 'bg-danger'
    }`;
    
    // Update active connections (could be real data)
    const activeConnections = {{ $stats['active_sessions'] }} + Math.floor(Math.random() * 3) - 1;
    document.getElementById('activeConnections').textContent = activeConnections;
    document.getElementById('activeConnectionsBar').style.width = `${Math.min(activeConnections * 5, 100)}%`;
}

// Initialize DataTable
$(document).ready(function() {
    $('.table').DataTable({
        "order": [[2, "desc"]],
        "pageLength": 5
    });
    
    // Update real-time metrics every 5 seconds
    // updateRealTimeMetrics();
    // setInterval(updateRealTimeMetrics, 5000);
});
</script>
@endpush