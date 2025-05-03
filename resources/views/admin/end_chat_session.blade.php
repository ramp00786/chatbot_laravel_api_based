@extends('layouts.app')

@section('title', 'End inactive sessions')

@section('content')
    <h1>Session Cleanup Logs</h1>
    <pre id="output">Connecting...</pre>


    <style>
        pre { background: #111; color: #0f0; padding: 20px; height: 400px; overflow: auto; }
    </style>

@endsection
@push('scripts')
    <script>
        const output = document.getElementById('output');
        const eventSource = new EventSource(`{{ url('/stream-end-inactive-sessions') }}`);

        eventSource.onmessage = function(event) {
            output.textContent += event.data + '\n';
            output.scrollTop = output.scrollHeight;
        };

        eventSource.onerror = function(error) {
            console.log(error)
            output.textContent += '\n[Error or finished streaming]\n';
            eventSource.close();
        };
    </script>
@endpush
