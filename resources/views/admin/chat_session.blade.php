@extends('layouts.app')

@section('title', 'Chat Session')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Chat Session History</h5>
                </div>
                <div class="card-body">
                    <div class="chat-container" style="height: 500px; overflow-y: auto; margin-bottom: 20px;">
                        @foreach($logs as $log)
                            <div class="mb-3">
                                <div class="d-flex justify-content-{{ $log->sender === 'user' ? 'end' : 'start' }}">
                                    <div class="card {{ $log->sender === 'user' ? 'bg-primary text-white' : 'bg-light' }}" style="max-width: 80%;">
                                        <div class="card-body">
                                            <strong>{{ ucfirst($log->sender) }}</strong>
                                            
                                            @if($log->type === 'list')
                                                @php
                                                    $jsonData = json_decode($log->message, true);
                                                   
                                                @endphp
                                                
                                                @if(is_array($jsonData) && isset($jsonData['questions']))
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($jsonData['questions'] as $question)
                                                            <li>{{ $question }}</li>
                                                        @endforeach
                                                    </ul>
                                                @elseif(is_array($jsonData))
                                                    <ul class="list-unstyled mb-0">
                                                        @foreach($jsonData as $key => $value)
                                                            @if(is_array($value))
                                                                {{print_r($value)}}
                                                                {{-- <li><strong>{{ ucfirst($key) }}:</strong> {{ implode(', ', $value) }}</li> --}}
                                                            @else
                                                                {{-- <li><strong>{{ ucfirst($key) }}:</strong> {{ $value }}</li> --}}
                                                            @endif
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p class="mb-0">{{ $log->message }}</p>
                                                @endif
                                            @else
                                                <p class="mb-0">{{ $log->message }}</p>
                                            @endif
                                        </div>
                                        <div class="card-footer text-muted small">
                                            @if($log->created_at instanceof \DateTime)
                                                {{ $log->created_at->format('M d, Y H:i') }}
                                            @else
                                                {{ $log->created_at }}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Session details section remains the same -->
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0">Session Details</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Started At:</strong> 
                                @if($session->started_at instanceof \DateTime)
                                    {{ $session->started_at->format('M d, Y H:i') }}
                                @else
                                    {{ $session->started_at }}
                                @endif
                            </p>
                            <p><strong>Status:</strong> 
                                <span class="badge {{ $session->ended_at ? 'bg-secondary' : 'bg-success' }}">
                                    {{ $session->ended_at ? 'Completed' : 'Active' }}
                                </span>
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>User:</strong> {{ $session->user->name ?? 'Guest' }}</p>
                            <p><strong>Email:</strong> {{ $session->user->email ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .chat-container {
        background-color: #f8f9fa;
        border-radius: 10px;
        padding: 15px;
    }
    .card-footer {
        background-color: rgba(0,0,0,0.03);
        border-top: 1px solid rgba(0,0,0,0.125);
    }
    .bg-light .card-body ul li {
        padding: 5px 0;
        border-bottom: 1px solid #eee;
    }
    .bg-primary .card-body ul li {
        padding: 5px 0;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }
    .bg-light .card-body ul li:last-child,
    .bg-primary .card-body ul li:last-child {
        border-bottom: none;
    }
</style>
@endsection