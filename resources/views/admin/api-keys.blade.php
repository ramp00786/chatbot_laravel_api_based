@extends('layouts.app')

@section('title', 'Manage API Keys')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">API Keys Management</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#generateKeyModal">
                    Generate New Key
                </button>
            </div>
        </div>
        <div class="card-body">
        <div class="container">
            @include('admin.questions.partials.errors')
            @yield('content')
        </div>


            

            @if(session('new_key'))
            <div class="alert alert-info">
                <strong>New Key Generated:</strong> 
                <code>{{ session('new_key') }}</code>
                <button class="btn btn-sm btn-outline-secondary copy-key" 
                        data-key="{{ session('new_key') }}"
                        title="Copy to clipboard"
                        onclick="copyToClipBoard('{{ session('new_key') }}');"
                        >
                    <i class="fas fa-copy"></i>
                </button>
            </div>
            @endif


            

            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Key</th>
                            <th>Status</th>
                            <th>Created At</th>
                            <th>Last Used</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($keys as $key)
                            <tr>
                                <td>
                                    <code class="text-truncate d-inline-block" style="max-width: 200px;">
                                        {{ $key->key }}
                                    </code>
                                    <button class="btn btn-sm btn-outline-secondary copy-key" 
                                            data-key="{{ $key->key }}"
                                            title="Copy to clipboard"
                                            onclick="copyToClipBoard('{{ $key->key }}');"
                                            >
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </td>
                                <td>
                                    <span class="badge {{ $key->is_active ? 'bg-success' : 'bg-secondary' }}">
                                        {{ $key->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>{{ $key->created_at->format('M d, Y H:i') }}</td>
                                <td>
                                    @if($key->last_used_at)
                                        {{ $key->last_used_at->format('M d, Y H:i') }}
                                    @else
                                        Never used
                                    @endif
                                </td>
                                <td>
                                    @if($key->is_active)
                                        <form action="{{ route('admin.api-keys.destroy', $key->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" 
                                                    onclick="return confirm('Are you sure you want to revoke this key?')">
                                                Revoke
                                            </button>
                                        </form>
                                    @else
                                        <form action="{{ route('admin.api-keys.update', $key->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="activate" value="1">
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Reactivate
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">No API keys found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Generate Key Modal -->
<div class="modal fade" id="generateKeyModal" tabindex="-1" aria-labelledby="generateKeyModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.api-keys.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="generateKeyModalLabel">Generate New API Key</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="keyName" class="form-label">Key Name (Optional)</label>
                        <input type="text" class="form-control" id="keyName" name="name">
                        <div class="form-text">Helps you identify this key later</div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="enableKey" name="is_active" checked value="1"
                            @checked(old('is_active', $apiKey->is_active ?? true))>
                            <label class="form-check-label" for="enableKey">
                                Enable Key Immediately
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Generate Key</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    
    
    document.addEventListener('DOMContentLoaded', function() {
        // Copy key to clipboard
        document.querySelectorAll('.copy-key').forEach(button => {
            button.addEventListener('click', function() {
                const key = this.getAttribute('data-key');
                navigator.clipboard.writeText(key).then(() => {
                    const originalHTML = this.innerHTML;
                    this.innerHTML = '<i class="fas fa-check"></i>';
                    setTimeout(() => {
                        this.innerHTML = originalHTML;
                    }, 2000);
                });
            });
        });
    });
</script>
@endsection

@section('styles')
<style>
    .copy-key {
        width: 30px;
        height: 30px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }
    code {
        background: #f8f9fa;
        padding: 2px 5px;
        border-radius: 3px;
    }
</style>
@endsection




<!-- 

<button type="button" class="button" onclick="colors_Success('default success');">Success</button>
<button type="button" class="button" onclick="colors_Error('default success');">Error</button>
<button type="button" class="button" onclick="colors_Warning('default success');">Warning</button>
<button type="button" class="button" onclick="colors_Info('default success');">Info</button>
-->