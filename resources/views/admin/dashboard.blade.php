@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h2 class="mb-4">Dashboard Overview</h2>
    
    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total Sessions</h5>
                    <h2>{{ $stats['total_sessions'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Active Sessions</h5>
                    <h2>{{ $stats['active_sessions'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <h5 class="card-title">Total Messages</h5>
                    <h2>{{ $stats['total_messages'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">Active API Keys</h5>
                    <h2>{{ $stats['active_api_keys'] }}</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Top Locations</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($locations as $location)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $location->location ?: 'Unknown' }}
                                <span class="badge bg-primary rounded-pill">{{ $location->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Device Distribution</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach($devices as $device)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                {{ $device->device_type }}
                                <span class="badge bg-primary rounded-pill">{{ $device->total }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Sessions -->
    <div class="card">
        <div class="card-header">
            <h5>Recent Chat Sessions</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Started At</th>
                            <th>Status</th>
                            <th>API Key</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentSessions as $session)
                            <tr>
                                <td>{{ $session->user_name }}</td>
                                <td><a href="{{route('admin.dashboard.session_id', $session->id)}}"> {{ $session->user_email }}</a> </td>
                                <td>{{ $session->started_at->format('M d, Y H:i') }}</td>
                                <td>
                                    <span class="badge {{ $session->ended_at ? 'bg-secondary' : 'bg-success' }}">
                                        {{ $session->ended_at ? 'Completed' : 'Active' }}
                                    </span>
                                </td>
                                <td>{{ \Illuminate\Support\Str::limit($session->apiKey->key, 10) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection