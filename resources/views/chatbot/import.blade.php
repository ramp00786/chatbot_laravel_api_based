@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Import Chatbot Questions') }}</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('chatbot.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="file">Select Excel File</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input" id="file" name="file" required>
                                <label class="custom-file-label" for="file">Choose file...</label>
                            </div>
                            <small class="form-text text-muted">
                                Please upload the Excel file with the course data. The file should have worksheets for:
                                <ul>
                                    <li>After 10</li>
                                    <li>After 12</li>
                                    <li>After Polytechnic</li>
                                    <li>After Graduation</li>
                                </ul>
                            </small>
                        </div>

                        <button type="submit" class="btn btn-primary mt-3">
                            <i class="fas fa-upload"></i> Import Data
                        </button>
                    </form>

                    <hr class="my-4">

                    <div class="alert alert-info">
                        <h5 class="alert-heading">Import Instructions</h5>
                        <p>The import process will:</p>
                        <ol>
                            <li>Create worksheet entries under "Explore Programs"</li>
                            <li>Import all courses in hierarchical structure</li>
                            <li>Add details (Duration, Fees, Link, Eligibility) for each course</li>
                        </ol>
                        <p class="mb-0">Duplicate entries will be automatically skipped.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Show the selected file name
    document.querySelector('.custom-file-input').addEventListener('change', function(e) {
        var fileName = document.getElementById("file").files[0].name;
        var nextSibling = e.target.nextElementSibling;
        nextSibling.innerText = fileName;
    });
</script>
@endsection